<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use App\Jobs\UserActivityLogJob;



class ActivityLogger
{
    public function handle(Request $request, Closure $next)
    {
        // Continue with request
        $response = $next($request);
       
        try {
            $user = Auth::user();
            $route = $request->route();
            $controller = $route?->getActionName() ?? 'system';
            $method = $request->method();
            $session = $request->session()->id();

            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                // Device & Machine Info
                $agent = new Agent();
                $machineId = $request->header('X-Machine-ID') 
                    ?? $agent->platform().'_'.$agent->device().'_'.$agent->browser();

                // Prepare activity data
                $logData = [
                    'user_id'     => $user->id ?? null,
                    'user_name'   => $user->name ?? 'Guest',
                    'module'      => class_basename($controller),
                    'action'      => $this->mapAction($method, $controller),
                    'method'      => $method,
                    'url'         => $request->fullUrl(),
                    'route'       => $route?->getName(),
                    'request_data'=> json_encode($request->except(['password', '_token']), JSON_UNESCAPED_SLASHES),
                    'headers'     => json_encode($request->headers->all(), JSON_UNESCAPED_SLASHES),
                    'ip_address'  => $request->ip(),
                    'user_agent'  => $request->userAgent(),
                    'machine_id'  => $machineId,
                    'session_id'  => $session ?? '',
                    'status_code' => $response->getStatusCode(),
                ];
                
                // dd($logData);
                UserActivityLogJob::dispatch($logData);   
            }         

        } catch (\Exception $e) {
            // never break app flow
        }

        return $response;
    }

    private function mapAction($method, $controller)
    {
        return match ($method) {
            'POST'   => "Created in {$controller}",
            'PUT', 
            'PATCH'  => "Updated in {$controller}",
            'DELETE' => "Deleted in {$controller}",
            'GET'    => "Viewed {$controller}",
            default  => "Action in {$controller}",
        };
    }
}
