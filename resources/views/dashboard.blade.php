<x-layouts.app.workspace :title="__('Dashboard')">
    <div class="flex flex-col gap-6 max-w-[1600px] w-full">
        <!-- Welcome Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Welcome back, Olasunkanmi 👋</h1>
                <p class="text-[13px] text-slate-500 mt-1">Here's what's happening today.</p>
            </div>
            
            <div class="shrink-0">
                <button class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    <flux:icon.calendar class="w-4 h-4 text-slate-400" />
                    May 20, 2025
                    <flux:icon.chevron-down class="w-4 h-4 text-slate-400 ml-1" />
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
            <!-- Total Patients -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-[13px] font-medium text-slate-500 mb-1">Total Patients</h3>
                        <div class="text-2xl font-bold text-slate-800">1,248</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 shrink-0">
                        <flux:icon.user-group class="w-5 h-5" variant="solid" />
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[11px] font-medium text-slate-500">
                    <span class="flex items-center text-emerald-600 gap-0.5"><flux:icon.arrow-up class="w-3 h-3" /> 12.5%</span> vs last month
                </div>
            </div>

            <!-- Appointments -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-[13px] font-medium text-slate-500 mb-1">Appointments</h3>
                        <div class="text-2xl font-bold text-slate-800">156</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                        <flux:icon.calendar-days class="w-5 h-5" variant="solid" />
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[11px] font-medium text-slate-500">
                    <span class="flex items-center text-emerald-600 gap-0.5"><flux:icon.arrow-up class="w-3 h-3" /> 8.3%</span> vs last month
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-[13px] font-medium text-slate-500 mb-1">Total Revenue</h3>
                        <div class="text-2xl font-bold text-slate-800">₦24,560,000</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <flux:icon.banknotes class="w-5 h-5" variant="solid" />
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[11px] font-medium text-slate-500">
                    <span class="flex items-center text-emerald-600 gap-0.5"><flux:icon.arrow-up class="w-3 h-3" /> 18.6%</span> vs last month
                </div>
            </div>

            <!-- Outstanding Bills -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-[13px] font-medium text-slate-500 mb-1">Open Invoices</h3>
                        <div class="text-2xl font-bold text-slate-800">14</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-[11px] font-medium text-slate-500">
                        <span class="flex items-center text-red-500 gap-0.5">Pending payment</span>
                    </div>
                    <a href="{{ route('billing') }}" class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700">View all</a>
                </div>
            </div>
        </div>

        <!-- Content Row 1: Recent Patients & Today's Schedule -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            <!-- Recent Patients (2 cols) -->
            <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
                <div class="px-5 py-4 flex items-center justify-between border-b border-slate-100 bg-white">
                    <h3 class="font-bold text-[14px] text-slate-800">Recent Patients / Customers</h3>
                    <a href="{{ route('patients.index') }}" class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700">View all</a>
                </div>
                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left text-[13px] whitespace-nowrap">
                        <thead class="text-[11px] text-slate-400 font-medium border-b border-slate-100 bg-white">
                            <tr>
                                <th class="px-5 py-3 font-medium">Patient ID</th>
                                <th class="px-5 py-3 font-medium">Name</th>
                                <th class="px-5 py-3 font-medium">Department</th>
                                <th class="px-5 py-3 font-medium">Last Visit</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3.5 text-slate-500 font-medium">PAT-0001</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-[10px] font-bold">AB</div>
                                        <span class="font-bold text-slate-700">Aisha Bello</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">Cardiology</td>
                                <td class="px-5 py-3.5 text-slate-500">May 20, 2025</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 text-emerald-600 text-[12px] font-semibold bg-emerald-50 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3.5 text-slate-500 font-medium">PAT-0002</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-bold">JA</div>
                                        <span class="font-bold text-slate-700">John Adeyemi</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">Orthopedics</td>
                                <td class="px-5 py-3.5 text-slate-500">May 19, 2025</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 text-emerald-600 text-[12px] font-semibold bg-emerald-50 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3.5 text-slate-500 font-medium">PAT-0003</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">MO</div>
                                        <span class="font-bold text-slate-700">Mary Okafor</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">Pediatrics</td>
                                <td class="px-5 py-3.5 text-slate-500">May 19, 2025</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 text-emerald-600 text-[12px] font-semibold bg-emerald-50 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3.5 text-slate-500 font-medium">PAT-0004</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center text-[10px] font-bold">EN</div>
                                        <span class="font-bold text-slate-700">Emeka Nwosu</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">General Practice</td>
                                <td class="px-5 py-3.5 text-slate-500">May 18, 2025</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 text-slate-500 text-[12px] font-semibold bg-slate-100 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3.5 text-slate-500 font-medium">PAT-0005</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-[10px] font-bold">FU</div>
                                        <span class="font-bold text-slate-700">Fatima Usman</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">Gynecology</td>
                                <td class="px-5 py-3.5 text-slate-500">May 18, 2025</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 text-emerald-600 text-[12px] font-semibold bg-emerald-50 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-[14px] text-slate-800">Today's Schedule</h3>
                    <a href="{{ route('scheduling') }}" class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700">View all</a>
                </div>
                
                <div class="flex flex-col gap-4">
                    <!-- Schedule Item -->
                    <div class="flex gap-4">
                        <div class="w-16 text-right shrink-0">
                            <span class="text-[13px] font-bold text-slate-800">09:00 AM</span>
                        </div>
                        <div class="flex-1 flex items-start justify-between pb-4 border-l-2 border-purple-200 pl-4 relative">
                            <div class="absolute w-2 h-2 bg-purple-500 rounded-full -left-[5px] top-1.5"></div>
                            <div>
                                <div class="text-[13px] font-bold text-slate-800">Consultation</div>
                                <div class="text-[12px] text-slate-500 mt-0.5">Aisha Bello</div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-600 text-[10px] font-bold tracking-wide">Cardiology</span>
                        </div>
                    </div>
                    
                    <!-- Schedule Item -->
                    <div class="flex gap-4">
                        <div class="w-16 text-right shrink-0">
                            <span class="text-[13px] font-bold text-slate-800">10:30 AM</span>
                        </div>
                        <div class="flex-1 flex items-start justify-between pb-4 border-l-2 border-blue-200 pl-4 relative">
                            <div class="absolute w-2 h-2 bg-blue-500 rounded-full -left-[5px] top-1.5"></div>
                            <div>
                                <div class="text-[13px] font-bold text-slate-800">Follow Up</div>
                                <div class="text-[12px] text-slate-500 mt-0.5">John Adeyemi</div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold tracking-wide">Orthopedics</span>
                        </div>
                    </div>

                    <!-- Schedule Item -->
                    <div class="flex gap-4">
                        <div class="w-16 text-right shrink-0">
                            <span class="text-[13px] font-bold text-slate-800">12:00 PM</span>
                        </div>
                        <div class="flex-1 flex items-start justify-between pb-4 border-l-2 border-emerald-200 pl-4 relative">
                            <div class="absolute w-2 h-2 bg-emerald-500 rounded-full -left-[5px] top-1.5"></div>
                            <div>
                                <div class="text-[13px] font-bold text-slate-800">Consultation</div>
                                <div class="text-[12px] text-slate-500 mt-0.5">Mary Okafor</div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold tracking-wide">Pediatrics</span>
                        </div>
                    </div>

                    <!-- Schedule Item -->
                    <div class="flex gap-4">
                        <div class="w-16 text-right shrink-0">
                            <span class="text-[13px] font-bold text-slate-800">02:00 PM</span>
                        </div>
                        <div class="flex-1 flex items-start justify-between pb-4 border-l-2 border-slate-200 pl-4 relative">
                            <div class="absolute w-2 h-2 bg-slate-400 rounded-full -left-[5px] top-1.5"></div>
                            <div>
                                <div class="text-[13px] font-bold text-slate-800">Procedure</div>
                                <div class="text-[12px] text-slate-500 mt-0.5">Emeka Nwosu</div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold tracking-wide">General</span>
                        </div>
                    </div>

                    <!-- Schedule Item -->
                    <div class="flex gap-4">
                        <div class="w-16 text-right shrink-0">
                            <span class="text-[13px] font-bold text-slate-800">03:30 PM</span>
                        </div>
                        <div class="flex-1 flex items-start justify-between pl-4 relative">
                            <div class="absolute w-2 h-2 bg-amber-500 rounded-full -left-[1px] top-1.5"></div>
                            <div>
                                <div class="text-[13px] font-bold text-slate-800">Consultation</div>
                                <div class="text-[12px] text-slate-500 mt-0.5">Fatima Usman</div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold tracking-wide">Gynecology</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row 2: Charts & Tasks -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 pb-6">
            
            <!-- Revenue Overview -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-[14px] text-slate-800">Revenue Overview</h3>
                    <button class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-lg shadow-sm text-[11px] font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        This Month <flux:icon.chevron-down class="w-3 h-3 text-slate-400" />
                    </button>
                </div>
                <div class="flex-1 min-h-[200px] flex items-end relative overflow-hidden rounded-lg bg-gradient-to-t from-indigo-50/50 to-transparent p-2">
                    <!-- Chart Placeholder SVG -->
                    <svg viewBox="0 0 100 50" preserveAspectRatio="none" class="w-full h-32 text-indigo-500 overflow-visible mt-auto">
                        <defs>
                            <linearGradient id="gradient" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="currentColor" stop-opacity="0.3"/>
                                <stop offset="100%" stop-color="currentColor" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <path d="M0,45 L15,40 L30,42 L45,30 L60,32 L75,18 L90,24 L100,8" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M0,45 L15,40 L30,42 L45,30 L60,32 L75,18 L90,24 L100,8 L100,50 L0,50 Z" fill="url(#gradient)" stroke="none" />
                        <!-- Data Points -->
                        <circle cx="15" cy="40" r="1.5" fill="white" stroke="currentColor" stroke-width="1" class="text-indigo-600" />
                        <circle cx="30" cy="42" r="1.5" fill="white" stroke="currentColor" stroke-width="1" class="text-indigo-600" />
                        <circle cx="45" cy="30" r="1.5" fill="white" stroke="currentColor" stroke-width="1" class="text-indigo-600" />
                        <circle cx="60" cy="32" r="1.5" fill="white" stroke="currentColor" stroke-width="1" class="text-indigo-600" />
                        <circle cx="75" cy="18" r="1.5" fill="white" stroke="currentColor" stroke-width="1" class="text-indigo-600" />
                        <circle cx="90" cy="24" r="1.5" fill="white" stroke="currentColor" stroke-width="1" class="text-indigo-600" />
                        <circle cx="100" cy="8" r="1.5" fill="white" stroke="currentColor" stroke-width="1" class="text-indigo-600" />
                    </svg>
                    <!-- Y Axis Labels -->
                    <div class="absolute left-2 top-2 bottom-6 flex flex-col justify-between text-[9px] text-slate-400 font-medium">
                        <span>₦30M</span>
                        <span>₦25M</span>
                        <span>₦20M</span>
                        <span>₦15M</span>
                        <span>₦10M</span>
                        <span>₦5M</span>
                        <span>₦0</span>
                    </div>
                    <!-- X Axis Labels -->
                    <div class="absolute left-10 right-2 bottom-1 flex justify-between text-[10px] text-slate-400 font-medium">
                        <span>May 1</span>
                        <span>May 6</span>
                        <span>May 11</span>
                        <span>May 16</span>
                        <span>May 20</span>
                    </div>
                </div>
            </div>

            <!-- Department Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-[14px] text-slate-800">Department Stats</h3>
                </div>
                <div class="flex-1 flex items-center justify-center gap-8">
                    <!-- Donut Chart Placeholder -->
                    <div class="relative w-40 h-40 shrink-0">
                        <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
                            <!-- Background Circle -->
                            <path stroke="#f1f5f9" stroke-width="7" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <!-- Segments -->
                            <path stroke="#8b5cf6" stroke-width="7" stroke-dasharray="28, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path stroke="#3b82f6" stroke-width="7" stroke-dasharray="22, 100" stroke-dashoffset="-28" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path stroke="#10b981" stroke-width="7" stroke-dasharray="18, 100" stroke-dashoffset="-50" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path stroke="#94a3b8" stroke-width="7" stroke-dasharray="16, 100" stroke-dashoffset="-68" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path stroke="#f59e0b" stroke-width="7" stroke-dasharray="16, 100" stroke-dashoffset="-84" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xl font-bold text-slate-800">28%</span>
                        </div>
                    </div>
                    
                    <!-- Legend -->
                    <div class="flex flex-col gap-3 w-full max-w-[140px]">
                        <div class="flex items-center justify-between text-[11px] font-medium text-slate-600">
                            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Cardiology</div>
                            <span class="font-bold text-slate-800">28%</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] font-medium text-slate-600">
                            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Orthopedics</div>
                            <span class="font-bold text-slate-800">22%</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] font-medium text-slate-600">
                            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Pediatrics</div>
                            <span class="font-bold text-slate-800">18%</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] font-medium text-slate-600">
                            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-slate-400"></span> General Practice</div>
                            <span class="font-bold text-slate-800">16%</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] font-medium text-slate-600">
                            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Gynecology</div>
                            <span class="font-bold text-slate-800">16%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Overview -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-[14px] text-slate-800">Tasks Overview</h3>
                    <a href="{{ route('tasks.index') }}" class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700">View all</a>
                </div>
                
                <div class="flex flex-col gap-5 flex-1 justify-center pl-2">
                    <div class="flex items-center gap-5">
                        <div class="relative w-10 h-10 flex items-center justify-center">
                            <svg viewBox="0 0 36 36" class="w-full h-full absolute inset-0 text-orange-100 transform -rotate-90">
                                <path stroke="currentColor" stroke-width="4" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path stroke="#f97316" stroke-width="4" stroke-dasharray="60, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <flux:icon.clock class="w-4 h-4 text-orange-500 relative z-10" variant="outline" />
                        </div>
                        <div>
                            <div class="text-[15px] font-bold text-slate-800 leading-none mb-1.5">12</div>
                            <div class="text-[12px] font-medium text-slate-500">Pending Tasks</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-5">
                        <div class="relative w-10 h-10 flex items-center justify-center">
                            <svg viewBox="0 0 36 36" class="w-full h-full absolute inset-0 text-blue-100 transform -rotate-90">
                                <path stroke="currentColor" stroke-width="4" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path stroke="#3b82f6" stroke-width="4" stroke-dasharray="30, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <flux:icon.arrow-path class="w-4 h-4 text-blue-500 relative z-10" variant="outline" />
                        </div>
                        <div>
                            <div class="text-[15px] font-bold text-slate-800 leading-none mb-1.5">5</div>
                            <div class="text-[12px] font-medium text-slate-500">In Progress</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-5">
                        <div class="relative w-10 h-10 flex items-center justify-center">
                            <svg viewBox="0 0 36 36" class="w-full h-full absolute inset-0 text-emerald-100 transform -rotate-90">
                                <path stroke="currentColor" stroke-width="4" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path stroke="#10b981" stroke-width="4" stroke-dasharray="100, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <flux:icon.check-circle class="w-4 h-4 text-emerald-500 relative z-10" variant="outline" />
                        </div>
                        <div>
                            <div class="text-[15px] font-bold text-slate-800 leading-none mb-1.5">3</div>
                            <div class="text-[12px] font-medium text-slate-500">Completed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app.workspace>
