<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSeeker extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'location_id',
        'hire_type',
        'business_unit_id',
        'am_id',
        'dm_id',
        'tl_id',
        'recruiter_id',
        'consultant_code',
        'consultant_name',
        'skill',
        'sap_id',
        'status_id',
        'form_status',
        'client_id',
        'poc',
        'client_reporting_manager',
        'quarter',
        'selection_date',
        'offer_date',
        'join_date',
        'qly_date',
        'backout_term_date',
        'backout_term_month',
        'backout_term_year',
        'po_end_date',
        'po_end_month',
        'po_end_year',
        'pay_rate',
        'loaded_cost',
        'pay_rate_1',
        'bill_rate',
        'gp_month',
        'otc',
        'otc_split',
        'msp_fees',
        'final_gp',
        'percentage_gp',
        'end_client',
        'lob',
        'bo_type',
        'remark1',
        'remark2',
        'sources',
        'maker_id',
        'checker_id',
        'po_maker_id',
        'po_checker_id',
        'finance_maker_id',
        'finance_checker_id',
        'backout_maker_id',
        'backout_checker_id',
        'job_seeker_type',
        'process_status',
        'actual_billing_value',
        'invoice_no',
        'type_of_attrition',
        'reason_of_attrition',
        'reason_of_rejection',
        'currency',
        'select_month',
        'join_month',
        'join_year',
        'source',
        'domain',
        'hire_status',
        'pay_rate_usd',
        'bill_rate_usd',
        'basic_pay_rate',
        'benefits',
        'gp_hour',
        'gp_hour_usd',
        'ctc_offered',
        'billing_value',
        'loaded_gp',
        'final_billing_value',
        'bd_absconding_term',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'location_id' => 'integer',
        'business_unit_id' => 'integer',
        'am_id' => 'integer',
        'dm_id' => 'integer',
        'tl_id' => 'integer',
        'recruiter_id' => 'integer',
        'status_id' => 'integer',
        'client_id' => 'integer',
        'maker_id' => 'integer',
        'checker_id' => 'integer',
        'po_maker_id' => 'integer',
        'po_checker_id' => 'integer',
        'finance_maker_id' => 'integer',
        'finance_checker_id' => 'integer',
        'backout_maker_id' => 'integer',
        'backout_checker_id' => 'integer',
        'pay_rate' => 'decimal:2',
        'bill_rate' => 'decimal:2',
        'pay_rate_1' => 'decimal:2',
        'gp_month' => 'decimal:2',
        'otc' => 'decimal:2',
        'otc_split' => 'decimal:2',
        'msp_fees' => 'decimal:2',
        'loaded_cost' => 'decimal:2',
        'final_gp' => 'decimal:2',
        'percentage_gp' => 'decimal:2',
        'pay_rate_usd' => 'decimal:2',
        'bill_rate_usd' => 'decimal:2',
        'basic_pay_rate' => 'decimal:2',
        'benefits' => 'decimal:2',
        'gp_hour' => 'decimal:2',
        'gp_hour_usd' => 'decimal:2',
        'ctc_offered' => 'decimal:2',
        'billing_value' => 'decimal:2',
        'loaded_gp' => 'decimal:2',
        'final_billing_value' => 'decimal:2',
        'actual_billing_value' => 'decimal:2',
        'selection_date' => 'date',
        'offer_date' => 'date',
        'join_date' => 'date',
        'qly_date' => 'date',
        'backout_term_date' => 'date',
        'po_end_date' => 'date',
        'join_month' => 'string',
        'backout_term_month' => 'string',
        'po_end_month' => 'string',
        'select_month' => 'string',
        'join_year' => 'integer',
        'backout_term_year' => 'integer',
        'po_end_year' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyMaster::class, 'company_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(BranchMaster::class, 'location_id');
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnitMaster::class, 'business_unit_id');
    }

    public function assistantManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'am_id');
    }

    public function deputyManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'dm_id');
    }

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'tl_id');
    }

    public function recruiter(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recruiter_id');
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Consultant::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusMaster::class, 'status_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function maker(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'maker_id');
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'checker_id');
    }

    public function poMaker(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'po_maker_id');
    }

    public function poChecker(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'po_checker_id');
    }

    public function financeMaker(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'finance_maker_id');
    }

    public function financeChecker(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'finance_checker_id');
    }

    public function backoutMaker(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'backout_maker_id');
    }

    public function backoutChecker(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'backout_checker_id');
    }
    public function scopeTemporary($query)
    {
        return $query->where('job_seeker_type', 'Temporary');
    }

    public function scopePermanent($query)
    {
        return $query->where('job_seeker_type', 'Permanent');
    }

    public function scopeForPoMaker($query)
    {
        return $query->where('job_seeker_type', 'Temporary')
            ->where('form_status', 'Approved')
            ->where('process_status', 2)
            ->where('status_id', function ($subQuery) {
                $subQuery->select('id')
                    ->from('status_masters')
                    ->where('status', 'Joined');
            });
    }

    public function scopeForPoChecker($query)
    {
        return $query->where('job_seeker_type', 'Temporary')
            ->where('form_status', 'Pending')
            ->where('process_status', 3);
    }

    public function scopeForFinanceMaker($query)
    {
        return $query->where('job_seeker_type', 'Permanent')
            ->where('form_status', 'Approved')
            ->where('process_status', 2)
            ->where('status_id', function ($subQuery) {
                $subQuery->select('id')
                    ->from('status_masters')
                    ->where('status', 'Joined');
            });
    }

    public function scopeForFinanceChecker($query)
    {
        return $query->where('job_seeker_type', 'Permanent')
            ->where('form_status', 'Pending')
            ->where('process_status', 5);
    }



    public function scopeForBackoutMaker($query)
    {
        return $query->whereIn('job_seeker_type', ['Temporary', 'Permanent'])
            ->where('form_status', 'Approved')
            ->whereIn('process_status', [2, 4])
            ->where('status_id', function ($subQuery) {
                $subQuery->select('id')
                    ->from('status_masters')
                    ->where('status', 'Joined');
            });
    }

    public function scopeForBackoutChecker($query)
    {
        return $query->whereIn('job_seeker_type', ['Temporary', 'Permanent'])
            ->where('form_status', 'Pending')
            ->where('process_status', 5);
    }
}