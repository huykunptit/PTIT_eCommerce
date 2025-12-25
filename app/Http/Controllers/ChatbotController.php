<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/chatbot/message",
     *     summary="Gửi tin nhắn đến chatbot AI",
     *     tags={"Chatbot"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(property="message", type="string", example="Có bao nhiêu sản phẩm đang có sẵn?"),
     *             @OA\Property(property="conversation_id", type="string", nullable=true, example="conv_123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Phản hồi từ chatbot",
     *         @OA\JsonContent(
     *             @OA\Property(property="response", type="string", example="Hiện tại có 150 sản phẩm đang có sẵn."),
     *             @OA\Property(property="conversation_id", type="string", example="conv_123")
     *         )
     *     )
     * )
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_id' => 'nullable|string',
        ]);

        $debugRequested = $request->boolean('debug');
        $debugAllowed = (bool) config('app.debug') || (bool) env('CHATBOT_UI_DEBUG', false);
        $debugEnabled = $debugRequested && $debugAllowed;

        $user = Auth::user();
        $message = $request->input('message');
        $conversationId = $request->input('conversation_id', 'conv_' . uniqid());

        // Lấy dữ liệu hệ thống để cung cấp context cho AI
        $systemData = $this->getSystemData($user);

        // Gọi FastAPI chatbot service
        try {
            $fastApiUrl = env('FASTAPI_URL', 'http://fastapi:8001');
            $response = Http::timeout(30)->post("{$fastApiUrl}/chatbot/chat", [
                'message' => $message,
                'conversation_id' => $conversationId,
                'user_id' => $user?->id,
                'system_data' => $systemData,
                'debug' => $debugEnabled,
            ]);

            if ($response->successful()) {
                $json = $response->json();
                $payload = [
                    'response' => $json['response'] ?? '...',
                    'conversation_id' => $conversationId,
                ];
                if ($debugEnabled && isset($json['debug'])) {
                    $payload['debug'] = $json['debug'];
                }
                return response()->json($payload);
            }

            // FastAPI có phản hồi nhưng không thành công
            
            $detail = null;
            try {
                $detail = $response->json();
            } catch (\Throwable $e) {
                $detail = $response->body();
            }

            Log::warning('Chatbot FastAPI non-2xx', [
                'status' => $response->status(),
                'detail' => $detail,
            ]);

            return response()->json([
                'message' => 'Chatbot service error',
                'response' => 'Xin lỗi, dịch vụ AI đang gặp sự cố. Vui lòng thử lại sau.',
                'conversation_id' => $conversationId,
                'debug' => $debugEnabled ? [
                    'fastapi_url' => $fastApiUrl,
                    'status' => $response->status(),
                    'detail' => $detail,
                ] : null,
            ], 502);
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Chatbot gateway error',
                'response' => 'Xin lỗi, tôi đang gặp sự cố kết nối đến dịch vụ AI. Vui lòng thử lại sau.',
                'conversation_id' => $conversationId,
                'debug' => $debugEnabled ? [
                    'fastapi_url' => env('FASTAPI_URL', 'http://fastapi:8001'),
                    'error_type' => get_class($e),
                    'error' => $e->getMessage(),
                ] : null,
            ], 503);
        }

        // Fallback response nếu FastAPI không khả dụng
        return response()->json([
            'response' => 'Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau.',
            'conversation_id' => $conversationId,
        ], 503);
    }

    /**
     * @OA\Get(
     *     path="/api/chatbot/token",
     *     summary="Lấy token để sử dụng chatbot",
     *     tags={"Chatbot"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="1|xxxxxxxxxxxx")
     *         )
     *     )
     * )
     */
    public function getToken(Request $request)
    {
        $user = Auth::user();
        $token = $user->createToken('chatbot-token', ['chatbot:use'])->plainTextToken;
        
        return response()->json([
            'token' => $token,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/chatbot/system-data",
     *     summary="Lấy dữ liệu hệ thống (số lượng, giá) cho chatbot",
     *     tags={"Chatbot"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dữ liệu hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_products", type="integer", example=150),
     *             @OA\Property(property="available_products", type="integer", example=120),
     *             @OA\Property(property="total_orders", type="integer", example=500),
     *             @OA\Property(property="user_orders", type="integer", example=5)
     *         )
     *     )
     * )
     */
    public function getSystemData($user = null)
    {
        $user = $user ?? Auth::user();

        $totalProducts = Product::where('status', 'active')->count();
        $availableProducts = Product::where('status', 'active')
            ->where('quantity', '>', 0)
            ->count();

        $totalOrders = Order::count();
        $userOrders = $user ? Order::where('user_id', $user->id)->count() : 0;

        return [
            'total_products' => $totalProducts,
            'available_products' => $availableProducts,
            'total_orders' => $totalOrders,
            'user_orders' => $userOrders,
        ];
    }

    public function index()
    {
        $systemData = $this->getSystemData();
        return response()->json($systemData);
    }
}

