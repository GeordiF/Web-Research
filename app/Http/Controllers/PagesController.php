<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
# imports the Google Cloud client library
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Barryvdh\Debugbar\Facade as Debugbar;
use Spatie\GoogleCalendar\Event;

class PagesController extends Controller {


    public function index() {
        session_start();

        $events = Event::get();
        $imageData = $this->readImageData();

        $activities = $imageData[0];
        $date = $imageData[1];
        $path = $imageData[2];

        return view('welcome', [
            'activities' => $activities,
            'Date' => $date,
            'Path' => $path,
            'Events' => $events,
        ]);
    }

    public function readImageData() {
        $imageAnnotator = new ImageAnnotatorClient();

        $path = "images/calender-filled-in.jpg";
        # annotate the image
        $image = file_get_contents($path);
        $response = $imageAnnotator->documentTextDetection($image);
        $annotation = $response->getFullTextAnnotation();

        $block_text_array = [];
        $Date = [];
        $activities = [];

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


        $date = implode(' ', $Date);

        return [$activities, $date, $path];
    }

    public function createEvent() {
        $hours = [];
        $ToDos = [];

        $data = $this->readImageData();

        $activities = $data[0];
        $date = $data[1];
        $date= str_replace("\n", "", $date);
        $date= str_replace("  ", " ", $date);

        $dateArray = explode(" ", $date);

        $day = $dateArray[0];
        $month = $dateArray[1];
        $year = $dateArray[2];

        $monthNumber = null;

        switch ($month) {
            case 'JANUARY':
                $monthNumber = "01";
                break;
            case 'FEBRUARY':
                $monthNumber = "02";
                break;
            case 'MARCH':
                $monthNumber = "03";
                break;
            case 'APRIL':
                $monthNumber = "04";
                break;
            case 'MAY':
                $monthNumber = "05";
                break;
            case 'JUNE':
                $monthNumber = "06";
                break;
            case 'JULY':
                $monthNumber = "07";
                break;
            case 'AUGUST':
                $monthNumber = "08";
                break;
            case 'SEPTEMBER':
                $monthNumber = "09";
                break;
            case 'OCTOBER':
                $monthNumber = "10";
                break;
            case 'NOVEMBER':
                $monthNumber = "11";
                break;
            case 'DECEMBER':
                $monthNumber = "12";
                break;
            default:
        }

        foreach ($activities as $activity) {
            $split_activity = explode(' UUR ', $activity);
            $plusHour = $split_activity[0] + 1;

            $dateTimeStringBegin = $year . '-' . $monthNumber . '-' . $day . ' ' . $split_activity[0] . ':00:00' ;
            $dateTimeStringEnd = $year . '-' . $monthNumber . '-' . $day . ' ' . $plusHour . ':00:00' ;

            $begin = Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeStringBegin, 'Europe/Brussels');
            $end = Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeStringEnd, 'Europe/Brussels');

            Event::create([
                'name' => $split_activity[1],
                'startDateTime' => $begin,
                'endDateTime' => $end,
            ]);
        }

        return redirect()->back()->with('success', 'The event has been created!');
    }

}
