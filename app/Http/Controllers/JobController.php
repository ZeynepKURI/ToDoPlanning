<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Developer;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::all();
        return response()->json($jobs);
    }

    public function store(Request $request)
    {
        $jobs = $request->input('jobs'); // Gelen veriyi al
    
        // Hatalı durumu kontrol et
        if (is_null($jobs) || !is_array($jobs)) {
            return response()->json(['status' => 'error', 'message' => 'Geçersiz veri formatı.'], 422);
        }
    
        foreach ($jobs as $job) {
            Job::create([
                'title' => $job['title'] ?? null,
                'time' => $job['time'] ?? null,
                'level' => $job['level'] ?? null,
            ]);
        }
    
        return response()->json(['status' => 'success', 'message' => 'Veriler başarıyla kaydedildi.']);
    }

    public function show($id)
    {
        $job = Job::findOrFail($id);
        return response()->json($job);
    }

    public function update(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $validatedData = $this->validateJob($request->all(), true); // Güncelleme için doğrulama
        $job->update($validatedData);
        return response()->json($job);
    }

    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();
        return response()->json(null, 204);
    }

    public function calculate()
    {
        $jobs = Job::all();
        $weeksRequired = $this->calculateCompletionTime($jobs->toArray());

        return response()->json(['weeks_required' => $weeksRequired]);
    }

    private function calculateCompletionTime($jobs)
    {
        $totalHours = array_sum(array_column($jobs, 'time'));
        $developersCount = Developer::count(); // Geliştirici sayısını dinamik al
        $weeklyHours = 45; // Haftalık çalışma saati
        return ceil($totalHours / ($developersCount * $weeklyHours));
    }

    private function validateJob($job, $update = false)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'time' => 'required|integer',
            'level' => 'required|integer',
        ];

        return validator($job, $rules)->validate();
    }

    public function fetchJobs()
    {
        // Sağlayıcılardan job'ları al (örnek veri)
        $jobs = [
            ['title' => 'Business Job 1', 'time' => 14, 'level' => 3],
            ['title' => 'Business Job 2', 'time' => 13, 'level' => 4],
        ];
        return response()->json(['status' => 'success', 'data' => $jobs]);
    }

    public function distributeJobs(Request $request)
    {
        // Job'ları geliştiricilere dağıt
        $jobs = $request->input('jobs');
        $developers = Developer::all();
        
        // Job dağıtım mantığını burada uygulayın
        $distribution = [];
        
        foreach ($jobs as $job) {
            foreach ($developers as $developer) {
                if (!isset($distribution[$developer->id])) {
                    $distribution[$developer->id] = [];
                }
                $distribution[$developer->id][] = $job;
                break; // İlk geliştiriciye ver
            }
        }

        return response()->json(['status' => 'success', 'distribution' => $distribution]);
    }
}
