<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upload pagina') }}
        </h2>
    </x-slot>

    <div class="py-12" data-bs-theme="dark">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 ml-4">
                    @if (session()->has('success'))
                        <div class="alert alert-success mt-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{route("upload.insert")}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 form-group">
                            <label for="file" class="form-label">Bestand</label>
                            <input type="file" name="file" id="formFile" class="form-control @error('file') is-invalid @enderror" accept="application/pdf">
                            <div id="fileHelp" class="form-text">Je kunt alleen .pdf bestanden uploaden</div>
                            @error("file")
                                <div class="alert alert-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-group">
                            <label for="password" class="form-label">Wachtwoord</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                            <div id="passwordHelp" class="form-text">(optioneel) Kies een wachtwoord om het bestand mee te beveiligen</div>
                            @error("password")
                                <div class="alert alert-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-group">
                            <button type="submit" class="btn btn-primary">Upload bestand</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
