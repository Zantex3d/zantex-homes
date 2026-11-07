<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $companyId = $request->header('X-Company-ID') 
                ?? session('company_id')
                ?? $request->user()->preferred_company_id;

            if ($companyId) {
                $company = \App\Models\Company::find($companyId);
                
                if ($company && $request->user()->companies()->where('company_id', $companyId)->exists()) {
                    request()->merge(['company_id' => $companyId]);
                    session(['company_id' => $companyId]);
                    app()->instance('company', $company);
                }
            }
        }

        return $next($request);
    }
}
