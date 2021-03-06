<?php

namespace App\Models;

use App\Casts\CustomType;
use App\Casts\EducationDetails;
use App\Concerns\HasPublications;
use App\Concerns\HasResearchCommittee;
use App\Models\Pivot\ScholarCoursework;
use App\Models\ScholarAppeal;
use App\Types\AdmissionMode;
use App\Types\FundingType;
use App\Types\Gender;
use App\Types\LeaveStatus;
use App\Types\RequestStatus;
use App\Types\ReservationCategory;
use App\Types\ScholarAppealTypes;
use App\Types\ScholarDocumentType;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Scholar extends User
{
    use Notifiable, HasPublications, HasResearchCommittee;

    protected $fillable = [
        'first_name', 'last_name',
        'email', 'password', 'gender', 'phone', 'address',
        'avatar_path',
        'category', 'admission_mode', 'funding',
        'registration_date', 'enrolment_id',
        'term_duration', 'research_area',
        'education_details',
        'proposed_title',
    ];

    protected $casts = [
        'registration_date' => 'datetime',
        'category' => CustomType::class . ':' . ReservationCategory::class,
        'admission_mode' => CustomType::class . ':' . AdmissionMode::class,
        'gender' => CustomType::class . ':' . Gender::class,
        'education_details' => EducationDetails::class,
        'funding' => CustomType::class . ':' . FundingType::class,
    ];

    protected $withCount = [
        'courseworks', 'completedCourseworks',
        'journals', 'conferences',
        'presentations',
        'advisoryMeetings',
        'leaves', 'approvedLeaves',
    ];

    protected $appends = [
        'name', 'avatar_url',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'avatar_path',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(static function ($scholar) {
            if ($scholar->education_details === null) {
                $scholar->education_details = [];
            }
        });

        static::created(static function ($scholar) {
            $scholar->courseworks()->attach(PhdCourse::core()->get());
        });
    }

    public function registrationValidUpto()
    {
        return optional($this->registration_date)->addYears($this->term_duration);
    }

    public function presentations()
    {
        return $this->hasMany(Presentation::class)->orderBy('date', 'DESC');
    }

    public function courseworks()
    {
        return $this->belongsToMany(PhdCourse::class)
            ->withPivot(['completed_on', 'marksheet_path', 'id'])
            ->using(ScholarCoursework::class);
    }

    public function completedCourseworks()
    {
        return $this->courseworks()->wherePivot('completed_on', '<>', null);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class)
            ->whereNull('extended_leave_id')
            ->orderBy('to', 'desc');
    }

    public function approvedLeaves()
    {
        return $this->leaves()->where('status', LeaveStatus::APPROVED);
    }

    public function advisoryMeetings()
    {
        return $this->hasMany(AdvisoryMeeting::class)->orderBy('date', 'desc');
    }

    public function documents()
    {
        return $this->hasMany(ScholarDocument::class)->orderBy('date', 'desc');
    }

    public function progressReports()
    {
        return $this->hasMany(ProgressReport::class)->orderBy('date', 'desc');
    }

    public function prePhdSeminar()
    {
        return $this->hasOne(PrePhdSeminar::class);
    }

    public function isJoiningLetterUploaded()
    {
        return $this->documents()->where('type', ScholarDocumentType::JOINING_LETTER)->exists();
    }

    public function isTableOfContentsOfThesisUploaded()
    {
        return $this->documents()->where('type', ScholarDocumentType::THESIS_TOC)->exists();
    }

    public function isPrePhdSeminarNoticeUploaded()
    {
        return $this->documents()->where('type', ScholarDocumentType::PRE_PHD_SEMINAR_NOTICE)->exists();
    }

    public function canApplyForPrePhdSeminar()
    {
        return $this->isJoiningLetterUploaded()
            && $this->courseworks_count
            && $this->areCourseworksCompleted()
            && $this->journals_count
            && $this->proposed_title;
    }

    public function titleApproval()
    {
        return $this->hasOne(TitleApproval::class);
    }

    public function canApplyForTitleApproval()
    {
        return $this->isJoiningLetterUploaded()
            && $this->isTableOfContentsOfThesisUploaded()
            && $this->isPrePhdSeminarNoticeUploaded()
            && $this->prePhdSeminar
            && $this->prePhdSeminar->isCompleted();
    }

    public function areCourseworksCompleted()
    {
        return $this->completed_courseworks_count === $this->courseworks_count;
    }

    public function examiner()
    {
        return $this->hasOne(ScholarExaminer::class);
    }

    // Helpers
    public function addCourse(PhdCourse $course, $attributes = [])
    {
        return $this->courseworks()->syncWithoutDetaching([$course->id => $attributes]);
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar_path != null && Storage::exists($this->avatar_path)) {
            return route('scholars.profile.avatar', $this);
        }

        return 'https://gravatar.com/avatar/'
            . md5(strtolower(trim($this->email)))
            . '?s=200&d=identicon';
    }

    // Accessors
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
