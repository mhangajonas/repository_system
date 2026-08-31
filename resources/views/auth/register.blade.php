<x-guest-layout>
    <!-- Kitufe cha Home juu kulia -->
    <div class="absolute top-6 right-6">
        <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            &larr; Home
        </a>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data="{ role: 'student' }">
        @csrf

        <!-- Full Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Institutional Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Institutional Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- User Role (Hapa ndipo mtumiaji anachagua ni nani) -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('User Role')" />
            <select id="role" name="role" x-model="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="student">Student</option>
                <option value="supervisor">Supervisor</option>
                <option value="librarian">Librarian</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Registration No. / Staff ID (Inabadilika yenyewe kulingana na Role) -->
        <div class="mt-4">
            <!-- Ikishakuwa student, inaonesha hii -->
            <template x-if="role === 'student'">
                <div>
                    <x-input-label for="reg_number" :value="__('Registration No.')" />
                    <x-text-input id="reg_number" class="block mt-1 w-full" type="text" name="reg_number" :value="old('reg_number')" placeholder="Mfano: T/2023/001" />
                    <x-input-error :messages="$errors->get('reg_number')" class="mt-2" />
                </div>
            </template>

            <!-- Ikishakuwa supervisor au librarian, inaonesha hii -->
            <template x-if="role === 'supervisor' || role === 'librarian'">
                <div>
                    <x-input-label for="staff_id" :value="__('Staff ID')" />
                    <x-text-input id="staff_id" class="block mt-1 w-full" type="text" name="staff_id" :value="old('staff_id')" placeholder="Mfano: STF/001" />
                    <x-input-error :messages="$errors->get('staff_id')" class="mt-2" />
                </div>
            </template>
        </div>

        <!-- Department -->
        <div class="mt-4">
            <x-input-label for="department" :value="__('Department')" />
            <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department')" />
            <x-input-error :messages="$errors->get('department')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>