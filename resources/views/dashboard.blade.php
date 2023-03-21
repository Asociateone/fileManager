<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table id="fileTable">
                        <thead>
                            <tr>
                                <th>Bestand</th>
                                <th>Download</th>
                                <th>Deel bestand</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                                <tr>
                                    <td>{{$file->original_name}}</td>
                                    @if($file->password !== null)
                                        <td><button class="btn btn-primary" onclick="$.fn.enterPassword(true, {{$file->id}})">Download bestand</button></td>
                                    @else
                                        <td><button class="btn btn-primary" onclick="$.fn.enterPassword(false, {{$file->id}})">Download bestand</button></td>
                                    @endif
                                    <td><button class="btn btn-primary" onclick="$.fn.shareFile({{$file->id}})">Deel bestand</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>

let table = new DataTable('#fileTable', {
    responsive: true
});

    (function($){
        $.fn.shareFile = function(file_id){

        }

        //Kijkt of je een wachtwoord in moet voeren en checkt of wachtwoord klopt
        $.fn.enterPassword = function(hasPassword, file_id){
            if(!hasPassword){
                location.href = "/upload/"+file_id+"/download/null";
            }else{

                //TODO: maak hier datatable van in plaats van een prompt om wachtwoord te hiden
                wachtwoord = prompt("vul wachtwoord in");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "/upload-password",
                    data: {
                        "file_id": file_id,
                        "password": wachtwoord,
                    },
                    success: function(success){
                        if(!success){
                            alert("Ongeldig wachtwoord");
                        }else{
                            location.href = "/upload/"+file_id+"/download/"+success;
                        }
                    }
                });
            }
        }
    })( jQuery );
</script>
