<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Authorize {{ $client->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">Authorization Request</h2>
        
        <p class="text-gray-700 text-center mb-6">
            <strong>{{ $client->name }}</strong> is requesting permission to access your account.
        </p>
        
        <!-- Scopes (optional) -->
        @if (count($scopes) > 0)
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">This application will be able to:</h3>
            <ul class="list-disc pl-5 text-sm text-gray-600">
                @foreach ($scopes as $scope)
                    <li>{{ $scope->description }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('passport.authorizations.approve') }}" class="flex justify-between space-x-4">
            @csrf
            <input type="hidden" name="state" value="{{ $request->state }}">
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <input type="hidden" name="auth_token" value="{{ $authToken }}">

            <button type="submit" class="w-full bg-[#1e3a8a] text-white px-4 py-2 rounded-md hover:bg-blue-800 transition">
                Authorize
            </button>
        </form>
        
        <form method="POST" action="{{ route('passport.authorizations.deny') }}" class="mt-4">
            @csrf
            @method('DELETE')
            <input type="hidden" name="state" value="{{ $request->state }}">
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <input type="hidden" name="auth_token" value="{{ $authToken }}">

            <button type="submit" class="w-full bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                Cancel
            </button>
        </form>
    </div>
</body>
</html>
