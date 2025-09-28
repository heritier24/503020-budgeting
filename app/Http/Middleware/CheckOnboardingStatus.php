<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOnboardingStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if ($user) {
            // Skip onboarding check for admin users
            if ($user->is_admin) {
                // If admin is trying to access onboarding, redirect to admin dashboard
                if ($request->routeIs('onboarding')) {
                    return redirect()->route('admin.dashboard');
                }
                return $next($request);
            }
            
            // Check if user has completed onboarding
            $hasBudgetConfig = $user->budgetConfig()->exists();
            $hasIncomeSources = $user->incomeSources()->exists();
            
            // If user is on dashboard but hasn't completed onboarding, redirect to onboarding
            if ($request->routeIs('dashboard') && (!$hasBudgetConfig || !$hasIncomeSources)) {
                return redirect()->route('onboarding');
            }
            
            // If user is on onboarding but has completed it, redirect to dashboard
            if ($request->routeIs('onboarding') && $hasBudgetConfig && $hasIncomeSources) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}