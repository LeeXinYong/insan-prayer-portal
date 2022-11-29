<?php

namespace App\Services;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DataTableRenderHelper
{
    /**
     * Render title for datatable
     *
     * @param Model $model
     * @param string $editRoute link for href
     * @param string $column
     * @return mixed|string
     */
    public static function renderTitle(Model $model, string $editRoute, string $column = "title", string $ability = "Update")
    {
        return (Auth::user()->{'can' . $ability}($model)) ?
            "<a href='" . $editRoute . "' class='text-gray-800 text-hover-primary'>" . $model->$column . "</a>" :
            $model->$column;
    }

    /**
     * Render multiple fields for datatable
     *
     * @param Model $model
     * @param array<string, ?callable|string> $columns
     * @param bool $flexColumn
     * @return string
     */
    public static function renderMultipleFields(Model $model, array $columns, bool $flexColumn = true, int $gap = 0): string
    {
        $result = [];
        array_walk($columns, function ($callable, $column) use ($model, &$result) {
            if (is_int($column) && is_string($callable)) {
                $column = $callable;
            }

            if (!is_callable($callable)) {
                $callable = fn($_) => $_;
            }

            if ($rendered = $callable($model->$column, $model))
                $result[] = $rendered;
        });

        return "<div class='d-flex gap-$gap " .
            ($flexColumn ? "flex-column align-items-start" : "flex-row align-items-center") .
            " justify-content-start'>" . implode($result) . "</div>";
    }

    public static function renderTextInitial(string $text, $color = 'info', $size = 35, $showTooltip = true): string
    {
        $textInitials = Str::of($text)->replaceMatches('/\b(\w)(\w*)/', '$1')->replaceMatches('/\W/', '')->upper()->substr(0, 2)->toString();
        $tooltip = $showTooltip ? "data-bs-toggle='tooltip' title='' data-bs-original-title='{$text}' aria-label='{$text}'" : "";

        if (preg_match('/^#?([a-f0-9]{6}|[a-f0-9]{3})$/i', $color)) {
            return "<span class='d-flex flex-center rounded-circle' style='height: {$size}px; width: {$size}px; background-color: $color; color: white' {$tooltip}>" . $textInitials . "</span>";
        }

        return "<span class='d-flex flex-center rounded-circle bg-$color text-reverse-$color' style='height: {$size}px; width: {$size}px;' {$tooltip}>" . $textInitials . "</span>";
    }

    public static function renderCollapsible(string $body, string $title = 'Expand', string $icon = null): string
    {
        if (null === $icon) {
            $icon = Blade::getCustomDirectives()["svg"]("icons/duotune/arrows/arr064.svg,svg-icon-8");
        }
        $id = "collapsible-" . Str::uuid()->toString();
        return '<div class="accordion accordion-icon-toggle" id="' . $id . '-accordion">' .
            '<div>' .
            '<div class="accordion-header d-flex collapsed justify-content-start align-items-baseline" data-bs-toggle="collapse" data-bs-target="#' . $id . '-accordion-details">' .
            '<h3 class="fs-8 fw-normal mb-0 me-1">' . $title . '</h3>' .
            '<span class="accordion-icon">' .
            $icon .
            '</span>' .
            '</div>' .
            '<div id="' . $id . '-accordion-details" class="fs-8 collapse" data-bs-parent="#' . $id . '-accordion">' .
            $body .
            '</div>' .
            '</div>';
    }

    public static function renderLocalisedFields(Model $model, string $column, int $truncate_line = 1): string
    {
        $truncate_line = max($truncate_line, 1);

        $columns = [
            "$column" => fn($_) => "<span class='fw-bold text-truncate-x-line' style='--truncate-line: " . $truncate_line . ";' title='" . $_ . "'>" . ($_) . "</span>",
            "localized_vi_$column" => fn($localized_vi_field, $model) => "<span class='text-muted fs-7 text-truncate-x-line' style='--truncate-line: " . $truncate_line . ";' title='" . ($localized_vi_field ?? $model->$column) . "'>" . ($localized_vi_field ?? $model->$column) . "</span>",
        ];
        return DataTableRenderHelper::renderMultipleFields($model, $columns);
    }

    /**
     * Render image for datatable
     *
     * @param Model $model
     * @param string $filePathField
     * @param string $disk
     * @param string|null $title
     * @param string $size
     * @return string
     */
    public static function renderImage(Model $model, string $filePathField, string $disk = "public", ?string $title = null, string $size = "100px"): string
    {
        if (is_null($title)) {
            $title = __("general.message.thumbnail");
        }

        $module = self::instanceToModuleSnakeCase($model);
        $full_path = route("getFile", ["file_module" => $module, "module_id" => $model->id, "file_path_field" => $filePathField, "file_name" => strtolower(pathinfo($model->$filePathField, PATHINFO_EXTENSION))]);
        $no_thumb = url(theme()->getDemo() . "/customize/media/error/no-thumb.png");

        return Storage::disk($disk)->exists($model->$filePathField ?? "") ?
            "<div class='min-w-50px'><a class='overlay read-more' target='_blank' data-fslightbox='lightbox-banner' href='$full_path'><div class='overlay-wrapper'><img src='$full_path' class='img-fluid' title='" . $title . "' width='$size' alt='" . $title . "'/></div><div class='overlay-layer bg-dark bg-opacity-25 img-fluid' style='width:$size'><i class='bi bi-eye-fill fs-2x text-white read-more'></i></div></a></div>"
            : "<img src='$no_thumb' class='img-fluid' title='" . __("general.message.not_available") . "' alt='" . __("general.message.not_available") . "' width='$size'/>";
    }

    /**
     * Render image for datatable
     *
     * @param Model $model
     * @param string $filePathField
     * @param string $thumbnailPathField
     * @param boolean $youtube_url
     * @param string $disk
     * @param string|null $title
     * @param string $size
     * @return string
     */
    public static function renderVideo(Model $model, string $filePathField, string $thumbnailPathField, bool $youtube_url, string $disk = "public", ?string $title = null, string $size = "150px"): string
    {
        if (is_null($title)) {
            $title = __("general.message.video");
        }

        $module = self::instanceToModuleSnakeCase($model);
        $thumbnail_full_path = route("getFile", ["file_module" => $module, "module_id" => $model->id, "file_path_field" => $thumbnailPathField]);
        $video_full_path = $youtube_url ? $filePathField : route("getFile", ["file_module" => $module, "module_id" => $model->id, "file_path_field" => $filePathField, "file_name" => strtolower(pathinfo($model->$filePathField, PATHINFO_EXTENSION))]);
        $no_thumb = url(theme()->getDemo() . "/customize/media/error/no-thumb.png");

        return Storage::disk($disk)->exists($model->$filePathField ?? "") || $youtube_url ?
            "<div class='min-w-50px'><a class='overlay read-more' target='_blank' data-fslightbox='lightbox-video' href='$video_full_path'><div class='overlay-wrapper'><img src='$thumbnail_full_path' class='img-fluid' title='" . $title . "' width='$size' alt='" . $title . "'/></div><div class='overlay-layer bg-dark bg-opacity-25 img-fluid' style='width:$size'><i class='fa fa-play-circle fs-2x text-white read-more'></i></div></a></div>"
            : "<img src='$no_thumb' class='img-fluid' title='" . __("general.message.not_available") . "' alt='" . __("general.message.not_available") . "' width='$size'/>";
    }

    /**
     * Render image for datatable
     *
     * @param Model $model
     * @param string $filePathField
     * @param string $thumbnailPathField
     * @param string $disk
     * @param string|null $title
     * @param string $size
     * @return string
     */
    public static function renderPdf(Model $model, string $filePathField, string $thumbnailPathField, string $disk = "public", ?string $title = null, string $size = "100px"): string
    {
        if (is_null($title)) {
            $title = __("general.message.thumbnail");
        }

        $module = self::instanceToModuleSnakeCase($model);
        $thumbnail_full_path = route("getFile", ["file_module" => $module, "module_id" => $model->id, "file_path_field" => $thumbnailPathField]);
        $pdf_full_path = route("getFile", ["file_module" => $module, "module_id" => $model->id, "file_path_field" => $filePathField, "file_name" => strtolower(pathinfo($model->$filePathField, PATHINFO_EXTENSION))]);
        $no_thumb = url(theme()->getDemo() . "/customize/media/error/no-thumb.png");

        return Storage::disk($disk)->exists($model->$filePathField ?? "") && Storage::disk($disk)->exists($model->$thumbnailPathField ?? "") ?
            "<div class='min-w-50px'><a class='overlay read-more view-pdf' target='_blank' data-fslightbox='lightbox-pdf' href='#pdf_frame' data-pdf-link=" . $pdf_full_path . "'><div class='overlay-wrapper'><img src='$thumbnail_full_path' class='img-fluid' title='" . $title . "' width='$size' alt='" . $title . "'/></div><div class='overlay-layer bg-dark bg-opacity-25 w-100px img-fluid'><i class='bi bi-eye-fill fs-2x text-white read-more'></i></div></a></div>"
            : "<img src='$no_thumb' class='img-fluid' title='" . __("general.message.not_available") . "' alt='" . __("general.message.not_available") . "' width='100px'/>";
    }

    /**
     * Render color for datatable
     *
     * @param Model $model
     * @param string $filePathField
     * @return string
     */
    public static function renderColor(Model $model, string $filePathField = 'color', $noText = false): string
    {
        return "<div class='d-flex align-items-center'><div class='h-15px w-15px rounded-circle me-1' style='background-color: {$model->$filePathField}'></div>" .
            ($noText ? "" : $model->$filePathField) .
            "</div>";
    }

    /**
     * Render date time for datatable
     *
     * @param Model $model
     * @param string $field
     * @param callable|null $urgent callable that returns a boolean, text become red if is true
     * @return string
     */
    public static function renderDateTime(Model $model, string $field = "updated_at", ?callable $urgent = null): string
    {
        $urgentFormatter = is_callable($urgent) ? function ($value, $default = null) use ($urgent) {
            return $urgent($value) ? "<span class='text-warning fw-bold'>" . ($default ?? $value) . "</span>" : ($default ?? $value);
        } : fn($value, $default = null) => ($default ?? $value);

        if ($model->$field != __("general.message.not_applicable")) {
            $duration = DateTimeFormatterService::formatIntervals($model->{$field . "_epoch"});

            return $urgentFormatter($model->{$field . "_epoch"}, $duration) . "<br>" .
                self::renderMutedField($model->$field, 'text-nowrap');
        } else {
            return $urgentFormatter($model->$field);
        }
    }

    /**
     * Render date for datatable
     *
     * @param Model $model
     * @param string $field
     * @param string $endfield if not null, will render end date
     * @return string
     */
    public static function renderDate(Model $model, string $field = "updated_at", string $endfield = null): string
    {
        if ($endfield == null) {
            if ($model->$field != __("general.message.not_applicable")) {
                return self::renderMutedField($model->$field);
            } else {
                return $model->$field;
            }
        } else {
            $range = $model->$field . " " . __("general.connectors.to") . " " . $model->$endfield;
            if ($model->$field != __("general.message.not_applicable") && $model->$endfield != __("general.message.not_applicable")) {
                return self::renderMutedField($range);
            } else {
                return $range;
            }
        }
    }

    /**
     * Render a small and muted field
     *
     * @param $string
     * @param $class
     * @return string
     */
    public static function renderMutedField($string, $class = ''): string
    {
        return "<span class='text-muted fs-7 $class'>" . $string . "</span>";
    }

    /**
     * Render badge for datatable, mostly for status
     *
     * @param Model $model
     * @param string $field
     * @param array $styles colors
     * @param array $values text
     * @param callable|null $callback special renderer
     * @return string
     */
    public static function renderBadge(
        Model     $model,
        string    $field = "status",
        array     $styles = [
            0 => "danger",
            1 => "success",
        ],
        array     $values = [
            0 => "general.message.inactive",
            1 => "general.message.active",
        ],
        ?callable $callback = null,
        bool      $withMarginTop = false
    ): string
    {
        if (!is_null($callback)) {
            [$style, $value] = $callback($model, $styles, $values);
        } else {
            $values = array_map(fn($value) => __($value), $values);

            try {
                $style = $styles[$model->$field];
            } catch (\Throwable $e) {
                $style = "info";
            }

            try {
                $value = $values[$model->$field];
            } catch (\Throwable $e) {
                $value = __("general.message.unknown");
            }
        }

        $badge = (!str_contains($style, "badge")) ? "badge-light-$style" : $style;
        return "<div class='badge $badge fw-bolder'" . ($withMarginTop ? " style='margin-top: calc(0.55rem + 1px);' " : "") . ">$value</div>";
    }

    /**
     * Render rating
     *
     * @param Model $model
     * @param string|callable $column
     * @param int $max
     * @param bool $showNumbers
     * @return string
     */
    public static function renderRating(Model $model, string|callable $column, int $max = 5, bool $showNumbers = false, $icon = '<i class="la la-star fs-4"></i>'): string
    {
        if (is_callable($column)) {
            $rating = $column($model);
        } else {
            $rating = $model->$column;
        }
        $rating = (int)$rating;

        return '<div class="rating" style="gap: 0.025rem;">' .
            implode('', array_map(fn($i) => '<div class="rating-label' . ($i <= $rating ? ' checked' : '') . '">' . $icon . '</div>', range(1, $max))) .
            ($showNumbers ? '(' . $rating . '/' . $max . ')' : '') .
            '</div>';
    }

    /**
     * Render url
     *
     * @param Model $model
     * @param string $field
     * @param string $text
     * @param string $target
     * @return string
     */
    public static function renderUrl(Model $model, string $field = "url", string $text = "", string $target = "_blank"): string
    {
        return "<a href='{$model->$field}' target='$target' class='datatable-url'>" . (($text == "") ? __("general.datatable.visit_website") : $text) . "<i class='fa fa-arrow-circle-right mx-1' aria-hidden='true'></i></a>";
    }

    /**
     * Render Mobile View
     *
     * @param Model $model
     * @param string $text
     * @return string
     */
    public static function renderMobileView(Model $model, string $text = ""): string
    {
        return "<span role='button' class='view-content fw-bold text-primary text-hover-active-primary'>" . (($text == "") ? __("general.datatable.view_content_mobile") : $text) . "</span>";
    }

    /**
     * Filter multiple fields rendered in the renderMultipleFields method
     *
     * @param array<string, ?callable> $columns
     * @return Closure
     */
    public static function filterMultipleFields(array $columns): Closure
    {
        return function ($query, $keyword) use ($columns) {
            $query->where(function ($query) use ($columns, $keyword) {
                foreach ($columns as $column => $callable) {
                    if (is_callable($callable)) {
                        $callable($query, $keyword);
                    } else {
                        $query->orWhere($column, "like", "%{$keyword}%");
                    }
                }
            });
        };
    }

    /**
     * Filter localized fields
     * Caution: This function must assist with 'joinLocalizedField' function in LocalizedModel
     *
     * @param string $column
     * @param string $joined_localized_fields_name
     * @param string $language_code
     * @return Closure
     */
    public static function filterLocalisedFields(string $column, string $joined_localized_fields_name = "localized_fields", string $language_code = "vi"): Closure
    {
        return function ($query, $keyword) use ($column, $joined_localized_fields_name, $language_code) {
            $query->where(function ($query) use ($keyword, $column, $joined_localized_fields_name, $language_code) {
                $json_encoded_keyword = str_replace("\\", "\\\\", substr(json_encode($keyword), 1, -1));
                $localized_field_column = str_contains($column, ".") ? explode(".", $column)[1] : $column;
                $query->where($column, "like", "%$keyword%")
                    ->orWhereRaw("LOWER($joined_localized_fields_name.{$language_code}_$localized_field_column) like LOWER(?)", ["%" . $json_encoded_keyword . "%"]);
            });
        };
    }

    public static function filterDateTime($column = "updated_at"): Closure
    {
        return function ($query, $keyword) use ($column) {
            // defaulted to system and user timezone
            $system_timezone_offset = Carbon::now(config("app.timezone"))->getOffsetString();
            $user_timezone_offset = Carbon::now(Auth::user()->timezone ?? config("app.timezone"))->getOffsetString();
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ($column, '$system_timezone_offset', '$user_timezone_offset'), " . DateTimeFormatterService::DBDateTimeFormatter($column, $user_timezone_offset) . ")  like ?", ["%$keyword%"]);
        };
    }

    /**
     * Filter single Date or Daterange with connectors if $endcolumn is not null
     *
     * @param array<string, ?callable> $columns
     * @return Closure
     */
    public static function filterDate($column = "updated_at", $endcolumn = null): Closure
    {
        return function ($query, $keyword) use ($column, $endcolumn) {
            // defaulted to system and user timezone
            $system_timezone_offset = Carbon::now(config("app.timezone"))->getOffsetString();
            $user_timezone_offset = Carbon::now(Auth::user()->timezone ?? config("app.timezone"))->getOffsetString();

            if ($endcolumn == null) {
                $query->whereRaw("DATE_FORMAT(CONVERT_TZ($column, '$system_timezone_offset', '$user_timezone_offset'), '%d %b %Y')  like ?", ["%$keyword%"]);
            } else {
                $queryStart = "DATE_FORMAT(CONVERT_TZ($column, '$system_timezone_offset', '$user_timezone_offset'), '%d %b %Y')";
                $queryEnd = "DATE_FORMAT(CONVERT_TZ($endcolumn, '$system_timezone_offset', '$user_timezone_offset'), '%d %b %Y')";
                $query->whereRaw("CONCAT(" . $queryStart . ",' " . __('general.connectors.to') . " ', " . $queryEnd . ")" . "  like ?", ["%$keyword%"]);
            }
        };
    }

    public static function filterStatus(
        string $column = "status",
        array  $values = [
            0 => "general.message.inactive",
            1 => "general.message.active",
        ]
    ): Closure
    {

        $values = array_map(fn($value) => __($value), $values);

        return function ($query, $keyword) use ($values, $column) {
            $sql = "CASE";
            foreach ($values as $key => $value) {
                $sql .= " WHEN $column = '$key' THEN '$value'";
            }
            $sql .= " END like ?";
            $query->whereRaw($sql, ["%$keyword%"]);
        };
    }

    private static function instanceToModuleSnakeCase(Model $model)
    {
        $module = explode("\\", get_class($model));
        return str(end($module))->snake()->lower();
    }
}
