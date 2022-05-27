<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen " style="background-color: #191414">

            <!-- Page Heading -->
            <header class="shadow max-h-16" style="background-color: #2c2525">
                <div class="max-w-7xl  mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2>
                        <div class="spotifyControllerHeader">
                            @auth
                                <a href="{{route('party') }}"> Dashboard </a>
                            @else 
                                <a href="{{route('login') }}"> Log in </a>
                                <a href="{{route('register') }}"> Register </a>
                            @endauth
                            
                            
                        </div>
                    </h2>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200 ">
                                <div>
                                    Welcome to Party Controller for Spotify!
                                </div>
                                <div>
                                    <p>
                                        This webapp enables a spotify premium user to allow anyone to add a song to their current
                                        queue.
            
                                        This website was created to allow multiple users to contribute
                                        to the queue of songs playing on one device.
                                        
                                        Start by creating an account, then link your spotify account.
                                        After that create a party and share the parties name and password with your friends.
            
                                        Users will be able to join your party and search the spotify catalog for a track to add 
                                        to your queue. No more asking for song recommendations.
            
                                        AS A PARTY GUEST:
                                            You will be able to queue any song onb the spotify catalog to the hosts device. 
                                            By linking your spotiofy account to your Party Controller account, you will be able to\
                                            view your spotify library and even add the currently playing song to your library.
            
            
                                    </p>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </main>


        </div>
    </body>
</html>

