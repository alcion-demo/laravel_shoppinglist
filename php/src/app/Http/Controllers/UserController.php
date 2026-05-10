<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * __construct
     */
    public function __construct(protected User $user)
    {}

    public function index(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $users = $this->user->userList($keyword);

        return view('admin.users.index', compact('users', 'keyword'));
    }
}
