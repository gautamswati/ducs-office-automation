<?php

namespace App\Http\Controllers;

use App\Models\PrePhdSeminar;
use App\Models\Scholar;
use App\Models\ScholarAppeal;
use App\Types\RequestStatus;
use App\Types\ScholarAppealTypes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PrePhdSeminarController extends Controller
{
    public function index(Scholar $scholar)
    {
        $this->authorize('viewAny', [PrePhdSeminar::class, $scholar]);

        return view('pre-phd-seminar.index', [
            'scholar' => $scholar,
        ]);
    }

    public function request(Scholar $scholar)
    {
        $this->authorize('create', [PrePhdSeminar::class, $scholar]);

        return view('pre-phd-seminar.show', [
            'scholar' => $scholar,
        ]);
    }

    public function show(Scholar $scholar, PrePhdSeminar $prePhdSeminar)
    {
        $this->authorize('view', [$prePhdSeminar, $scholar]);

        return view('pre-phd-seminar.show', [
            'scholar' => $scholar,
        ]);
    }

    public function apply(Scholar $scholar)
    {
        $this->authorize('create', [PrePhdSeminar::class, $scholar]);

        $scholar->prePhdSeminar()->create([
            'status' => RequestStatus::APPLIED,
        ]);

        flash('Request for Pre-PhD Seminar applied successfully!')->success();

        return redirect(route('scholars.pre-phd-seminar.index', $scholar));
    }

    public function forward(Request $request, Scholar $scholar, PrePhdSeminar $prePhdSeminar)
    {
        $this->authorize('forward', [$prePhdSeminar, $scholar]);

        $prePhdSeminar->update([
            'status' => RequestStatus::RECOMMENDED,
        ]);

        flash("Scholar's appeal forwarded successfully!")->success();

        return redirect()->back();
    }

    public function schedule(Request $request, Scholar $scholar, PrePhdSeminar $prePhdSeminar)
    {
        $this->authorize('addSchedule', [$prePhdSeminar, $scholar]);

        $validSchedule = $request->validateWithBag('scheduleSeminar', [
            'scheduled_on' => ['required', 'date', 'after:today'],
        ]);

        $prePhdSeminar->update($validSchedule);

        flash('Pre PhD seminar schedule added successfully!')->success();

        return redirect(route('scholars.pre-phd-seminar.index', $scholar));
    }

    public function finalize(Request $request, Scholar $scholar, PrePhdSeminar $prePhdSeminar)
    {
        $this->authorize('finalize', [$prePhdSeminar, $scholar]);

        $validData = $request->validateWithBag('finalizeSeminar', [
            'finalized_title' => ['required', 'string'],
        ]);

        $prePhdSeminar->update($validData + ['status' => RequestStatus::APPROVED]);

        flash("Scholar's appeal finalized successfully!")->success();

        return redirect(route('scholars.pre-phd-seminar.index', $scholar));
    }
}
