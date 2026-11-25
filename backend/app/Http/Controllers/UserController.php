<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserFcm;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\HandlesValidation;
use App\Services\MediaService;
use App\Services\SettingService;
use App\Services\CurrencyService;
class UserController extends Controller
{
    use HandlesValidation;

    public function register_user(Request $request)
    {

        $rules = [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'mobile' => 'required|numeric|unique:users,mobile',
            'country_code' => 'required|string|max:255',
            'fcm_id' => 'nullable|string|max:255',
            'referral_code' => 'nullable|string|unique:users,referral_code|max:255',
            'friends_code' => 'nullable|string|max:255',
            'password' => 'required|string|max:255',
        ];

        if ($response = $this->HandlesValidation($request, $rules)) {
            return $response;
        } else {
            if ($request->filled('friends_code')) {
                $friends_code = $request->input('friends_code');
                $friend = User::where('referral_code', $friends_code)->first();

                if (!$friend) {
                    $response = [
                        'error' => true,
                        'error_message' => 'Invalid friends code! Please pass the valid referral code of the inviter',
                        'data' => [],
                    ];
                    return response()->json($response);
                }
            }

            $additional_data = [
                'username' => $request->username,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'country_code' => $request->country_code,
                'fcm_id' => $request->fcm_id,
                'referral_code' => $request->referral_code,
                'friends_code' => $request->friends_code,
                'type' => 'phone',
                'role_id' => 2,
            ];
            $identity_column = config('auth.defaults.passwords') === 'users.email' ? 'email' : 'mobile';
            $identity = ($identity_column == 'mobile') ? $request->mobile : $request->email;
            $lastInsertId = User::insertGetId($additional_data);

            if ($lastInsertId) {
                User::where($identity_column, $identity)->update(['active' => 1]);

                $data = User::select('users.id', 'users.username', 'users.email', 'users.mobile', 'c.name as city_name')
                    ->where($identity_column, $identity)
                    ->leftJoin('cities as c', 'c.id', '=', 'users.city')
                    ->groupBy('users.email')
                    ->get()
                    ->toArray();

                foreach ($data as $row) {
                    $row = outputEscaping($row);
                    $tempRow = [
                        'id' => isset($row['id']) && !empty($row['id']) ? $row['id'] : '',
                        'username' => isset($row['username']) && !empty($row['username']) ? $row['username'] : '',
                        'email' => isset($row['email']) && !empty($row['email']) ? $row['email'] : '',
                        'mobile' => isset($row['mobile']) && !empty($row['mobile']) ? $row['mobile'] : '',
                        'city_name' => isset($row['city_name']) && !empty($row['city_name']) ? $row['city_name'] : '',
                        'area_name' => isset($row['area_name']) && !empty($row['area_name']) ? $row['area_name'] : '',
                    ];

                    $rows[] = $tempRow;
                }
                $response = [
                    'error' => false,
                    'message' => 'Registered Successfully',
                    'data' => $rows,
                ];
                return response()->json($response);
            } else {
                $response = [
                    'error' => false,
                    'message' => 'Registration Fail',
                    'data' => [],
                ];
                return response()->json($response);
            }
        }
    }

    public function login(Request $request)
    {
        $loginInput = $request->input('login') ?? $request->input('mobile') ?? $request->input('email') ?? $request->input('username');

        if (empty($loginInput)) {
            return response()->json([
                'error' => true,
                'message' => 'Please enter your email, username, or mobile number.',
            ], 422);
        }

        if (empty($request->password)) {
            return response()->json([
                'error' => true,
                'message' => 'Password is required.',
            ], 422);
        }

        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : (is_numeric($loginInput) ? 'mobile' : 'username');

        $credentials = [
            $fieldType => $loginInput,
            'password' => $request->password,
        ];

        $attempt = auth()->attempt($credentials);
        if (!$attempt && $fieldType === 'mobile') {
            $attempt = auth()->attempt(['username' => $loginInput, 'password' => $request->password]);
        }

        if ($attempt) {
            $user = User::with('role')
                ->where('active', 1)
                ->find(Auth::user()->id);
            if ($user) {
                $fcm_ids = fetchDetails(UserFcm::class, ['user_id' => $user->id], 'fcm_id');

                $fcm_ids_array = array_map(function ($item) {
                    return $item->fcm_id;
                }, $fcm_ids);

                $user_data = [
                    'id' => $user->id ?? '',
                    'ip_address' => $user->ip_address ?? '',
                    'username' => $user->username ?? '',
                    'email' => $user->email ?? '',
                    'mobile' =>  $user->mobile ?? '',
                    'image' => app(MediaService::class)->getMediaImageUrl($user->image, 'USER_IMG_PATH'),
                    'balance' => $user->balance ?? '0',
                    'created_on' => $user->created_on ?? '',
                    'last_login' => $user->last_login ?? '',
                    'active' => $user->active ?? '',
                    'company' => $user->company ?? '',
                    'address' => $user->address ?? '',
                    'bonus' => $user->bonus ?? '',
                    'cash_received' => $user->cash_received ?? '0.00',
                    'dob' => $user->dob ?? '',
                    'country_code' => $user->country_code ?? '',
                    'city' => $user->city ?? '',
                    'area' => $user->area ?? '',
                    'street' => $user->street ?? '',
                    'pincode' => $user->pincode ?? '',
                    'fcm_id' => array_values($fcm_ids_array) ?? '',
                    'created_at' => $user->created_at ?? '',
                    'type' => $user->type ?? '',
                ];
                $request->session()->put('user_data', $user_data);

                return response()->json([
                    'error' => false,
                    'message' => 'Login successful',
                    'user' => $user_data,
                    'location' => route('admin.home'),
                ], 200);
            }
        }

        return response()->json([
            'error' => true,
            'message' => 'Invalid credentials',
        ], 401);
    }
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'error' => false,
            'message' => "User Logged out Successfully.",
            'redirect' => url('/')
        ], 200);
    }
    public function Profilelogout()
    {
        Auth::logout();
        return redirect(url('/'));
    }


    public function transactions_list($user_id = '', $type = '', $transaction_type = '')
    {
        $offset = request()->input('offset', 0);
        $limit = request()->input('limit', 10);
        $sort = request()->input('sort', 'id');
        $order = request()->input('order', 'ASC');
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');
        $search = trim(request()->input('search', ''));
        $filterUserId = request()->input('user_id', $user_id);

        $currency_symbol = app(CurrencyService::class)->getDefaultCurrency()->symbol ?? '';

        // Base Eloquent query
        $query = Transaction::with('user')
            ->when($filterUserId, fn($q) => $q->where('user_id', $filterUserId))
            ->when($transaction_type, fn($q) => $q->where('transaction_type', $transaction_type))
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->orWhere('id', 'like', "%$search%")
                        ->orWhere('amount', 'like', "%$search%")
                        ->orWhere('created_at', 'like', "%$search%")
                        ->orWhere('type', 'like', "%$search%")
                        ->orWhere('status', 'like', "%$search%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('username', 'like', "%$search%")
                                ->orWhere('mobile', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%");
                        });
                });
            });

        // Count total
        $total = $query->count();

        // Get paginated results
        $transactions = $query->orderBy($sort, $order)
            ->skip($offset)
            ->take($limit)
            ->get();

        // Status labels
        $status_labels = [
            'success' => '<span class="badge bg-success">Success</span>',
            'pending' => '<span class="badge bg-info">Pending</span>',
            'awaiting' => '<span class="badge bg-info">Awaiting</span>',
            'Failed' => '<span class="badge bg-danger">Failed</span>',
        ];

        // Format data
        $rows = $transactions->map(function ($txn) use ($currency_symbol, $status_labels) {
            return [
                'id' => $txn->id,
                'type' => str_replace('_', ' ', $txn->type),
                'order_id' => $txn->order_id,
                'txn_id' => $txn->txn_id,
                'payu_txn_id' => $txn->payu_txn_id,
                'amount' => $currency_symbol . $txn->amount,
                'status' => $status_labels[$txn->status] ?? '<span class="badge bg-primary">' . e($txn->status) . '</span>',
                'message' => $txn->message,
                'created_at' => Carbon::parse($txn->created_at)->format('d-m-Y'),
            ];
        });

        return response()->json([
            'total' => $total,
            'rows' => $rows,
        ]);
    }

    public function wallet_withdrawal_request($user_id)
    {
        $offset = request()->input('offset', 0);
        $limit = request()->input('limit', 10);
        $sort = request()->input('sort', 'id'); // Sort by column from PaymentRequest only
        $order = request()->input('order', 'ASC');
        $search = trim(request()->input('search'));
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');
        $payment_request_status = request()->input('payment_request_status');

        $query = PaymentRequest::with('user'); // eager-load user

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('amount_requested', 'like', "%$search%")
                    ->orWhere('payment_address', 'like', "%$search%");
            });
        }

        if ($startDate && $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        }

        if (isset($payment_request_status)) {
            $query->where('status', intval($payment_request_status));
        }

        if (!empty($user_id)) {
            $query->where('user_id', $user_id);
        }

        $total = $query->count();

        $results = $query->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get();

        $rows = [];

        foreach ($results as $row) {
            $rows[] = [
                'id' => $row->id,
                'payment_address' => $row->payment_address,
                'amount_requested' => app(CurrencyService::class)->formateCurrency(formatePriceDecimal($row->amount_requested)),
                'remarks' => $row->remarks,
                'status' => match ((string) $row->status) {
                    '0' => '<span class="badge bg-success">Pending</span>',
                    '1' => '<span class="badge bg-primary">Approved</span>',
                    '2' => '<span class="badge bg-danger">Rejected</span>',
                    default => '<span class="badge bg-secondary">Unknown</span>',
                },
                'date_created' => Carbon::parse($row->created_at)->format('Y-m-d'),
                'username' => optional($row->user)->username, // safe access
            ];
        }

        return response()->json([
            "rows" => $rows,
            "total" => $total,
        ]);
    }
}
