@extends('layouts.frontend')

@section('title', 'GAMUT-C | Demo')

@section('content')
<div class="pt-32 pb-24 bg-surface min-h-screen">
    <div class="max-w-4xl mx-auto px-margin-page">
        <!-- Header -->
        <div class="mb-12">
            <h1 class="font-headline-xl text-headline-xl text-trust-navy mb-4">Fully Working GAMUT-C Demo</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant">
                We offer fully functional demo installations for you to try out. Some simple configuration has been added for clearer demonstration of GAMUT-C, medical billing, access controls and patient portal. Each demo is reset overnight so no data is persistent.
            </p>
        </div>

        <!-- System Demo -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm mb-12 overflow-hidden">
            <div class="p-8 border-b border-outline-variant/30 bg-trust-navy/5">
                <h2 class="font-headline-lg text-headline-lg text-trust-navy">GAMUT-C Demo</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Access the main clinical and administrative system.</p>
            </div>
            
            <div class="p-8 space-y-8">
                <div>
                    <h3 class="font-headline-md text-headline-md text-trust-navy mb-4">Links</h3>
                    <div class="grid gap-4">
                        <a href="{{ route('login') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-trust-navy/30 hover:bg-trust-navy/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Staff Login (General)</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/login') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-trust-navy group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('doctor.login') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-trust-navy/30 hover:bg-trust-navy/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Doctor & Clinician Portal</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/doctor/login') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-trust-navy group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('hospital.login') }}" class="flex items-center justify-between p-4 rounded-xl border border-rose-200 hover:border-rose-400/50 hover:bg-rose-50/50 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Hospital Administration Portal</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/hospital/login') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-rose-600 group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('pharmacy.login') }}" class="flex items-center justify-between p-4 rounded-xl border border-indigo-200 hover:border-indigo-400/50 hover:bg-indigo-50/50 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Pharmacy Portal</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/pharmacy/login') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-trust-navy/30 hover:bg-trust-navy/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Staff Registration</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/register') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-trust-navy group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('kyc') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-trust-navy/30 hover:bg-trust-navy/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Identity Verification (KYC Onboarding)</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/kyc') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-trust-navy group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('subscribe') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-trust-navy/30 hover:bg-trust-navy/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Manage Subscription Plan</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/subscribe') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-trust-navy group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-headline-md text-headline-md text-trust-navy mb-4">Credentials</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b-2 border-outline-variant/50">
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy whitespace-nowrap">Username</th>
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy whitespace-nowrap">Password</th>
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy">Description</th>
                                </tr>
                            </thead>
                            <tbody class="font-body-md text-body-md">
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">admin@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Admin User (Super Admin)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">sarah@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Dr. Sarah Chen (Attending)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">james@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Dr. James Wilson (Attending)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">mike@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Dr. Mike Rivera (Resident)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">lisa@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Lisa Park (Clinical Staff)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">karen@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Karen Miller (Billing Admin)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">pharmacy@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">PharmD. Elena Ruiz (Pharmacist)</td>
                                </tr>
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">test@example.com</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Test User</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Portal Demo -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-outline-variant/30 bg-growth-sage/10">
                <h2 class="font-headline-lg text-headline-lg text-trust-navy">Patient Portal Demo</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Experience the system from the patient's perspective.</p>
            </div>
            
            <div class="p-8 space-y-8">
                <div>
                    <h3 class="font-headline-md text-headline-md text-trust-navy mb-4">Links</h3>
                    <div class="grid gap-4">
                        <a href="{{ route('patient.login') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-growth-sage/30 hover:bg-growth-sage/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Patient Portal Login</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/patient/login') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-growth-sage group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                        <a href="{{ route('patient.register') }}" class="flex items-center justify-between p-4 rounded-xl border border-outline-variant hover:border-growth-sage/30 hover:bg-growth-sage/5 transition-all group">
                            <div>
                                <h4 class="font-label-md text-label-md font-bold text-trust-navy">Patient Portal Registration</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ url('/patient/register') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-trust-navy/50 group-hover:text-growth-sage group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-headline-md text-headline-md text-trust-navy mb-4">Patient Credentials</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b-2 border-outline-variant/50">
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy">Email</th>
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy">Password</th>
                                    <th class="py-3 px-4 font-label-md text-label-md font-bold text-trust-navy">Description</th>
                                </tr>
                            </thead>
                            <tbody class="font-body-md text-body-md">
                                <tr class="border-b border-outline-variant/30 hover:bg-surface-container-lowest/50">
                                    <td class="py-3 px-4 font-data-mono">guardian@bloomemr.test</td>
                                    <td class="py-3 px-4 font-data-mono">password</td>
                                    <td class="py-3 px-4 text-on-surface-variant">Parent / Guardian (use Patient Portal Login)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
