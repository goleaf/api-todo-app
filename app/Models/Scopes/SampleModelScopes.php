<?php

namespace App\Models\Scopes;

trait SampleModelScopes
{
    /**
     * Filter by name that matches the given pattern.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $pattern
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNameLike($query, $pattern)
    {
        return $query->where('name', 'like', $pattern);
    }

    /**
     * Filter by exact name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeName($query, $value)
    {
        return $query->where('name', $value);
    }

    /**
     * Filter by name that starts with the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNameStartsWith($query, $value)
    {
        return $query->where('name', 'like', $value . '%');
    }

    /**
     * Filter by name that ends with the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNameEndsWith($query, $value)
    {
        return $query->where('name', 'like', '%' . $value);
    }

    /**
     * Filter by name that contains the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNameContains($query, $value)
    {
        return $query->where('name', 'like', '%' . $value . '%');
    }

    /**
     * Filter by email that matches the given pattern.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $pattern
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmailLike($query, $pattern)
    {
        return $query->where('email', 'like', $pattern);
    }

    /**
     * Filter by exact email.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmail($query, $value)
    {
        return $query->where('email', $value);
    }

    /**
     * Filter by email that starts with the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmailStartsWith($query, $value)
    {
        return $query->where('email', 'like', $value . '%');
    }

    /**
     * Filter by email that ends with the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmailEndsWith($query, $value)
    {
        return $query->where('email', 'like', '%' . $value);
    }

    /**
     * Filter by email that contains the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmailContains($query, $value)
    {
        return $query->where('email', 'like', '%' . $value . '%');
    }

    /**
     * Filter by description that matches the given pattern.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $pattern
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescriptionLike($query, $pattern)
    {
        return $query->where('description', 'like', $pattern);
    }

    /**
     * Filter by exact description.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescription($query, $value)
    {
        return $query->where('description', $value);
    }

    /**
     * Filter by description that starts with the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescriptionStartsWith($query, $value)
    {
        return $query->where('description', 'like', $value . '%');
    }

    /**
     * Filter by description that ends with the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescriptionEndsWith($query, $value)
    {
        return $query->where('description', 'like', '%' . $value);
    }

    /**
     * Filter by description that contains the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescriptionContains($query, $value)
    {
        return $query->where('description', 'like', '%' . $value . '%');
    }

    /**
     * Filter by status that matches the given pattern.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $pattern
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatusLike($query, $pattern)
    {
        return $query->where('status', 'like', $pattern);
    }

    /**
     * Filter by exact status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $value)
    {
        return $query->where('status', $value);
    }

    /**
     * Filter by status that starts with the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatusStartsWith($query, $value)
    {
        return $query->where('status', 'like', $value . '%');
    }

    /**
     * Filter by status that ends with the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatusEndsWith($query, $value)
    {
        return $query->where('status', 'like', '%' . $value);
    }

    /**
     * Filter by status that contains the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatusContains($query, $value)
    {
        return $query->where('status', 'like', '%' . $value . '%');
    }

    /**
     * Filter by exact position.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePosition($query, $value)
    {
        return $query->where('position', $value);
    }

    /**
     * Filter by position greater than the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePositionGreaterThan($query, $value)
    {
        return $query->where('position', '>', $value);
    }

    /**
     * Filter by position less than the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePositionLessThan($query, $value)
    {
        return $query->where('position', '<', $value);
    }

    /**
     * Filter by position between the given values.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $min
     * @param int $max
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePositionBetween($query, $min, $max)
    {
        return $query->whereBetween('position', [$min, $max]);
    }

    /**
     * Filter where is_active is true.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter where is_active is false.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotIsActive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Filter by published_at before the given date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublishedAtBefore($query, $date)
    {
        return $query->where('published_at', '<', $date);
    }

    /**
     * Filter by published_at after the given date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublishedAtAfter($query, $date)
    {
        return $query->where('published_at', '>', $date);
    }

    /**
     * Filter by published_at between the given dates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $start
     * @param string|\Carbon\Carbon $end
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublishedAtBetween($query, $start, $end)
    {
        return $query->whereBetween('published_at', [$start, $end]);
    }

    /**
     * Filter by published_at date (ignoring time).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublishedAtDate($query, $date)
    {
        return $query->whereDate('published_at', $date);
    }

    /**
     * Filter by settings that contains the given key or key/value pair.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $key
     * @param mixed|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSettingsContains($query, $key, $value = null)
    {
        if (func_num_args() === 2) {
            return $query->whereJsonContains('settings', $key);
        }
        return $query->whereJsonContains('settings', [$key => $value]);
    }

    /**
     * Filter by created_at before the given date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedAtBefore($query, $date)
    {
        return $query->where('created_at', '<', $date);
    }

    /**
     * Filter by created_at after the given date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedAtAfter($query, $date)
    {
        return $query->where('created_at', '>', $date);
    }

    /**
     * Filter by created_at between the given dates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $start
     * @param string|\Carbon\Carbon $end
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedAtBetween($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Filter by created_at date (ignoring time).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedAtDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Filter by updated_at before the given date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpdatedAtBefore($query, $date)
    {
        return $query->where('updated_at', '<', $date);
    }

    /**
     * Filter by updated_at after the given date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpdatedAtAfter($query, $date)
    {
        return $query->where('updated_at', '>', $date);
    }

    /**
     * Filter by updated_at between the given dates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $start
     * @param string|\Carbon\Carbon $end
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpdatedAtBetween($query, $start, $end)
    {
        return $query->whereBetween('updated_at', [$start, $end]);
    }

    /**
     * Filter by updated_at date (ignoring time).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpdatedAtDate($query, $date)
    {
        return $query->whereDate('updated_at', $date);
    }

    /**
     * Filter by exact id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeId($query, $value)
    {
        return $query->where('id', $value);
    }

    /**
     * Filter by id greater than the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIdGreaterThan($query, $value)
    {
        return $query->where('id', '>', $value);
    }

    /**
     * Filter by id less than the given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIdLessThan($query, $value)
    {
        return $query->where('id', '<', $value);
    }

    /**
     * Filter by id between the given values.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $min
     * @param int $max
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIdBetween($query, $min, $max)
    {
        return $query->whereBetween('id', [$min, $max]);
    }}
