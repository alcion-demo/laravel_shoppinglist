<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserStoreRequest;
use App\Services\UserService;

class UserController extends Controller
{
    /**
     * __construct
     */
    public function __construct(protected User $user, protected UserService $userService)
    {}

    public function index(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $users = $this->user->userList($keyword);

        return view('admin.users.index', compact('users', 'keyword'));
    }

    /**
     * ユーザー編集表示
     * @param App\Models\User
     * @return view
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * ユーザー編集表示
     * @param App\Models\User
     * @return view
     */
    public function create(User $user)
    {
        return view('admin.users.create', compact('user'));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->validated();

        $data['is_admin'] = $request->has('is_admin');
        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', 'ユーザーを更新しました');
    }

    /**
     * 投稿内容削除
     * @param App\Models\User
     * @return view
     */
    public function destroy(User $user)
    {
        $user->todos()->delete();

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'ユーザーを削除しました');
    }

    public function store(UserStoreRequest $request, UserService $userService)
    {
        $data = $request->validated();
        $data['is_admin'] = $request->has('is_admin');
        $userService->createUser($data);

        return redirect()->route('admin.users.index')->with('status', '管理者を追加しました');
    }

}
