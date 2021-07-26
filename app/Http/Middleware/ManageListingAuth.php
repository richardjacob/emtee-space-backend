<?php

namespace App\Http\Middleware;

use App\Models\Space;
use Closure;

class ManageListingAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) 
    {
        $space_id = $request->space_id;
        if(!isset($space_id)) {
            if(request()->wantsJson()) {
                return json_encode(['redirect' => route('space')]);
            }
            return redirect()->route('space');
        }

        $space = Space::find($space_id);
        if($space != '') {
            if($space->user_id == auth()->user()->id) {
                return $next($request); 
            }
        }
        if(request()->wantsJson()) {
            return json_encode(['redirect' => route('space')]);
        }
        return redirect()->route('space');
    }
}
