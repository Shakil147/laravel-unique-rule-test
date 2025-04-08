<?php

namespace Shakil147\UniqueRule\Rules;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Rule;

class UniqueData implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @param string $table
     * @param string $column
     * @param int $organizationId
     * @param int|null $excludeId
     */
    public function __construct(
        protected  $table,
        protected  $column = "id",
        protected  $excludeId = null,
        protected $excludeDeletedAt = true,
        private ?int $bussinessId = null,
        protected $query = [],
        protected $customMessage = 'The :attribute is in valid.',
    ) {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $query = DB::table($this->table)
            ->where($this->column, $value)
            ->when(count($this->query), fn($q) => $q->where($this->query));

        if ($this->excludeDeletedAt) {
            $query->whereNull('deleted_at');
        }
        if ($this->bussinessId) {
            $query->where('bussiness_id',$this->bussinessId);
        }
        if ($this->excludeId) {
            $query->where('id', '!=', $this->excludeId);
        }
        // if($query->exists()){
        //     dd($query->first());
        // }
        return $query->exists() ?  false : true;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->customMessage;
    }
}
