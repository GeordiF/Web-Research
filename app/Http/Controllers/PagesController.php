<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
# imports the Google Cloud client library
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use App\Library\Services\GoogleCalender;

class PagesController extends Controller {


    public function index(GoogleCalender $googleCalender) {

        session_start();
        $client = $googleCalender->getClient();

        if (isset($_SESSION['token'])) {
            $client->setAccessToken($_SESSION['token']);
        }

        if ($client->getAccessToken()) {
            print "yes";
        }else
        {
            print "no";
        }

        $imageData = $this->readImageData();

        $activities = $imageData[0];
        $date = $imageData[1];
        $path = $imageData[2];

        return view('welcome', [
            'activities' => $activities,
            'Date' => $date,
            'Path' => $path
        ]);
    }

    public function connect(GoogleCalender $googleCalender)
    {
        $client = $googleCalender->getClient();
        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function store(GoogleCalender $googleCalender)
    {
        $client = $googleCalender->getClient();
        $authCode = request('code');
        // Load previously authorized credentials from a file.
        $credentialsPath = storage_path('keys/client_secret_generated.json');
        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        // Store the credentials to disk.
        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, json_encode($accessToken));
        return redirect('/')->with('message', 'Credentials saved');
    }

    public function getResources(GoogleCalender $googleCalender)
    {
        // Get the authorized client object and fetch the resources.
        $client = $googleCalender->oauth();
        return $googleCalender->getResource($client);

    }

    public function readImageData() {
        $imageAnnotator = new ImageAnnotatorClient();

        $path = "images/Calender-filled-in.jpg";
        # annotate the image
        $image = file_get_contents($path);
        $response = $imageAnnotator->documentTextDetection($image);
        $annotation = $response->getFullTextAnnotation();

        $block_text_array = [];
        $Date = [];
        $activities = [];
        $hours = [];
        $ToDos = [];

        # print out detailed and structured information about document text
        if ($annotation) {
            foreach ($annotation->getPages() as $page) {
                foreach ($page->getBlocks() as $block) {
                    $block_text = '';
                    foreach ($block->getParagraphs() as $paragraph) {
                        foreach ($paragraph->getWords() as $word) {
                            foreach ($word->getSymbols() as $symbol) {

                                $block_text .= $symbol->getText();
                            }
                            $block_text .= ' ';
                        }
                        $block_text .= "\n";
                    }
                    array_push($block_text_array, $block_text);
                }
            }
        }
        else {

            print('No text found' . PHP_EOL);
        }
        foreach ($block_text_array as $block_text_item) {
            if (count($Date) < 2) {
                array_push($Date, $block_text_item);
                unset($block_text_item);
            }
            else {

                $hours = preg_split('| (?=\d{1,} UUR)|', $block_text_item);
                foreach ($hours as $hour) {
                    array_push($activities, $hour);
                }
            }
        }
        $imageAnnotator->close();

        foreach ($activities as $activity) {
            $split_activity = explode(' UUR ', $activity);

            array_push($hours, $split_activity[0]);
            array_push($ToDos, $split_activity[1]);

        }

        $date = implode(' ', $Date);

        return [$activities, $date, $path];
    }
}
