<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        
        // الحصول على الشهر المحدد أو الشهر الحالي
        $selectedMonth = $request->get('month', \Carbon\Carbon::now()->format('Y-m'));
        
        try {
            $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);
        } catch (\Exception $e) {
            $monthDate = \Carbon\Carbon::now();
            $selectedMonth = $monthDate->format('Y-m');
        }
        
        $startOfMonth = $monthDate->copy()->startOfMonth();
        $endOfMonth = $monthDate->copy()->endOfMonth();
        
        // جلب جميع الاجتماعات في الشهر المحدد
        $meetings = Meeting::where('organization_id', $organizationId)
            ->whereBetween('meeting_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->with(['project', 'responsibleEmployee'])
            ->orderBy('meeting_date', 'asc')
            ->orderBy('meeting_time', 'asc')
            ->get();
        
        // تنظيم الاجتماعات حسب التاريخ
        $meetingsByDate = [];
        foreach ($meetings as $meeting) {
            $dateKey = $meeting->meeting_date->format('Y-m-d');
            if (!isset($meetingsByDate[$dateKey])) {
                $meetingsByDate[$dateKey] = [];
            }
            $meetingsByDate[$dateKey][] = $meeting;
        }
        
        // حساب إحصائيات الشهر
        $totalMeetings = $meetings->count();
        $todayMeetings = $meetings->where('meeting_date', \Carbon\Carbon::today()->format('Y-m-d'))->count();
        $upcomingMeetings = $meetings->where('meeting_date', '>=', \Carbon\Carbon::today()->format('Y-m-d'))->count();
        
        return view('meetings.index', compact('meetings', 'meetingsByDate', 'selectedMonth', 'monthDate', 'totalMeetings', 'todayMeetings', 'upcomingMeetings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $projects = Project::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('business_name')
            ->get();
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        return view('meetings.create', compact('projects', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'meeting_date' => 'required|date',
            'meeting_time' => 'required',
            'title' => 'required|string|max:255',
            'objective' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'meeting_link' => 'nullable|url',
            'responsible_employee_id' => 'required|exists:employees,id',
            'attendees' => 'nullable|array',
            'attendees.*' => 'exists:employees,id'
        ]);

        $data = $request->all();
        $data['organization_id'] = $request->user()->organization_id;
        
        // تنظيف البيانات الفارغة للحضور
        if (isset($data['attendees']) && is_array($data['attendees'])) {
            $data['attendees'] = array_filter($data['attendees']);
            $data['attendees'] = array_values($data['attendees']);
            if (empty($data['attendees'])) {
                $data['attendees'] = null;
            }
        } else {
            $data['attendees'] = null;
        }

        Meeting::create($data);

        return redirect()->route('meetings.index')
            ->with('success', 'تم إضافة الاجتماع بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Meeting $meeting)
    {
        if ($meeting->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $meeting->load(['project', 'organization', 'responsibleEmployee']);
        
        // جلب بيانات الموظفين المطلوب حضورهم
        $attendees = [];
        if ($meeting->attendees && is_array($meeting->attendees)) {
            $attendees = Employee::whereIn('id', $meeting->attendees)->get();
        }

        return view('meetings.show', compact('meeting', 'attendees'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Meeting $meeting)
    {
        if ($meeting->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $organizationId = $request->user()->organization_id;
        $projects = Project::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('business_name')
            ->get();
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('meetings.edit', compact('meeting', 'projects', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meeting $meeting)
    {
        if ($meeting->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $request->validate([
            'meeting_date' => 'required|date',
            'meeting_time' => 'required',
            'title' => 'required|string|max:255',
            'objective' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'meeting_link' => 'nullable|url',
            'responsible_employee_id' => 'required|exists:employees,id',
            'attendees' => 'nullable|array',
            'attendees.*' => 'exists:employees,id'
        ]);

        $data = $request->all();
        
        // تنظيف البيانات الفارغة للحضور
        if (isset($data['attendees']) && is_array($data['attendees'])) {
            $data['attendees'] = array_filter($data['attendees']);
            $data['attendees'] = array_values($data['attendees']);
            if (empty($data['attendees'])) {
                $data['attendees'] = null;
            }
        } else {
            $data['attendees'] = null;
        }

        $meeting->update($data);

        return redirect()->route('meetings.index')
            ->with('success', 'تم تحديث الاجتماع بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Meeting $meeting)
    {
        if ($meeting->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $meeting->delete();

        return redirect()->route('meetings.index')
            ->with('success', 'تم حذف الاجتماع بنجاح');
    }
}
