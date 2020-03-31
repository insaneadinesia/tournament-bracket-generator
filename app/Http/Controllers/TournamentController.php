<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Tournament;
use App\Models\TournamentBracket;

class TournamentController extends Controller
{
    public function process(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return redirect('/')->withErrors($validator);
        }
        
        // Validate Participant Size
        $participant_size = $request->input('participant_size');
        
        do {
            if ($participant_size % 2 != 0) {
                return redirect('/')->withErrors('Participant size not valid');
                break;
            }

            $participant_size = $participant_size / 2;
        } while ($participant_size != 2);

        $tournament = Tournament::create();

        $match_no = 1;
        $bucket = [];
        $result = [];
        $bucket_a = [];
        $bucket_b = [];
        $participant_size = $request->input('participant_size');

        // Seperate participant into two different bucket
        for ($i=1; $i <= ($participant_size / 2); $i++) {
            if ($i % 2 == 0) {
                array_push($bucket_b, $i, $participant_size - ($i - 1));
                continue;
            }

            array_push($bucket_a, $i, $participant_size - ($i - 1));
        }

        // Merge into one bucket (no with sequential)
        $bucket = array_merge($bucket_a, $bucket_b);

        // Populate First Round
        for ($i=0; $i < count($bucket); $i+=2) {
            $data = [
                'tournament_id' => $tournament->id,
                'match_no' => $match_no,
                'team_a' => $bucket[$i],
                'team_b' => $bucket[$i + 1],
                'description' => 'Round of ' . count($bucket),
            ];

            array_push($result, $data);
            $match_no++;
        }

        // Populate Next Round
        $match_left = count($result);

        do {
            $arr = array_slice($result, count($result) - $match_left, $match_left);
            $desc = 'Round of '. $match_left;

            if ($match_left == 4) {
                $desc = 'Semi Final';
            }

            if ($match_left == 2) {
                $desc = 'Final';
            }

            for ($i=0; $i < $match_left; $i+=2) {
                $data = [
                    'tournament_id' => $tournament->id,
                    'match_no' => $match_no,
                    'team_a' => 'Winner match ' . $arr[$i]['match_no'],
                    'team_b' => 'Winner match ' . $arr[$i + 1]['match_no'],
                    'description' => $desc,
                ];

                array_push($result, $data);
                $match_no++;
            }

            $match_left = $match_left / 2;
        } while ($match_left != 1);

        $arr = array_slice($result, count($result) - 3, 2);
        
        $data = [
            'tournament_id' => $tournament->id,
            'match_no' => $match_no,
            'team_a' => 'Loser match ' . $arr[0]['match_no'],
            'team_b' => 'Loser match ' . $arr[1]['match_no'],
            'description' => 'Consolation Match',
        ];
        
        array_push($result, $data);

        // Bulk insert
        TournamentBracket::insert($result);

        return redirect()->route('bracket-result', ['hash' => $tournament->hash]);
    }

    public function result(Request $request)
    {
        $hash = $request->input('hash');
        $tournament = Tournament::where('hash', $hash)->with('brackets')->firstOrFail();

        return view('result', $tournament);
    }

    private function rules()
    {
        return [
          'participant_size' => 'required|min:4|numeric',
        ];
    }
}
