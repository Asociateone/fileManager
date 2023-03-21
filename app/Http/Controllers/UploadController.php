<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UploadController extends Controller
{
    public function page(){
        return view("upload.page");
    }

    public function uploaden(Request $request){
        $validated = $request->validate([
            "file" => ["required"],
            "password" => ["nullable"],
        ]);

        // Als de file bestaat en hij door de validation is gekomen validate de extensie van de file
        if($request->hasFile('file')){
            $allow_upload = $this->validateExtension($validated["file"]);
            // Als file extension failed dan redirect met error, withInput heeft op het moment geen zin omdat je geen oude inserted files kunt terugzetten als value.
            if(!$allow_upload){
                return redirect()->back()->withErrors(["file" => "U kunt alleen bestanden met de .pdf extensie."])->withInput();
            }

            // Upload bestand naar de server en slaat hem op in de database
            $file_uploaded = $this->handleUploadedFile($validated["file"], $validated["password"]);
            if(!$file_uploaded){
                return redirect()->back()->withErrors(["file" => "Het bestand is onsuccesvol geupload, probeer het later opnieuw."])->withInput();
            }

            return redirect()->route("upload.page")->withSuccess("Succesvol bestand geupload.");
        }
    }

    public function downloaden($file_id, $password){
        $file = File::whereId($file_id)->whereUserId(Auth::user()->id)->first();

        // Als file id niet bestaat of niet gelinked is aan de user.
        if($file === null){
            abort(500);
        }

        // Kijk of password goed is als er een password aan gelinked is.
        if($file->password != null && (base64_decode($password) != $file->password)){
            abort(500);
        }

        return Storage::download("/".$file->user_id."/".$file->upload_name, $file->original_name);
    }

    public function guessPassword(Request $request){
        $request->validate([
            "file_id" => "required|integer",
            "password" => "required|string",
        ]);

        $file = File::whereId($request->file_id)->whereUserId(Auth::user()->id)->first();

        // Als file id niet bestaat of niet gelinked is aan de user.
        if($file === null){
            abort(500);
        }

        if(!Hash::check($request->password, $file->password)){
            return false;
        }
        return base64_encode($file->password);
    }

    /**
     *
     * Kijkt of the extensie van de geuploade file klopt met de toegestaande extensies.
     */
    private function validateExtension(UploadedFile $file){
        $allowed_extensions = ["pdf"];
        $extension = $file->getClientOriginalExtension();
        $allow_upload = false;

        // Future proof willen we meerdere extensies toelaten
        if(in_array($extension, $allowed_extensions)){
            $allow_upload = true;
        }

        return $allow_upload;
    }

    /**
     *
     * Slaat de uploaded file op in de storage map en slaat hem ook op in de database.
     */
    private function handleUploadedFile(UploadedFile $file, $password = null){
        //Haalt de ingelogde user en timestamp op zodat het een unieke file name is.
        $user = Auth::user();
        $upload_file_name = time().".".$file->getClientOriginalExtension();
        // Slaat file op in /user_id/unix_timestamp.file_extension
        $uploaded_file = Storage::put("/".$user->id."/".$upload_file_name, $file->getContent());
        if(!$uploaded_file){
            return false;
        }

        // Sla de geuploade file op in de database
        $dbFile = new File();
        $dbFile->original_name = $file->getClientOriginalName();
        $dbFile->extension = $file->getClientOriginalExtension();
        $dbFile->upload_name = $upload_file_name;
        $dbFile->user_id = $user->id;

        //Als wachtwoord is aangemaakt voor de file voeg die ook toe in de database.
        if($password !== null){
            $dbFile->password = Hash::make($password);
        }
        $dbFile->save();

        return true;
    }
}
