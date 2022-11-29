<div class="scroll" id="activities-list">
    <div class="d-none d-flex justify-content-center" id="no_activity">
        @include("pages.common-components._empty-state-table", [
            "table" => "activities-list",
            "img" => "/demo3/customize/media/empty-states/activity.svg",
            "message" => __("user.message.no_recent_activity"),
            "force_show" => true,
        ])
    </div>
</div>

<div id="timeline-template" class="d-none">
    <div data-date='' class="mb-6 date_section">
        <div class="fs-6 fw-bold text-dark activity_created_at_date"></div>
        <div class="activities_section">
            <div class="d-flex flex-stack position-relative mt-6 activity-item">
                <!--begin::Bar-->
                <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                <!--end::Bar-->
                <!--begin::Info-->
                <div class="fw-semibold ms-5">
                    <!--begin::Time-->
                    <div class="fs-7 mb-1 activity_created_at_time"></div>
                    <!--end::Time-->
                    <!--begin::Title-->
                    <div class="fs-5 fw-bolder text-dark mb-2 activity_activity"></div>
                    <!--end::Title-->
                    <!--begin::User-->
                    <div class="fs-7 text-muted">
                        {{ __("user.view_label.details") }}: <span class="text-primary activity_description"></span>
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Info-->
            </div>
        </div>
    </div>

    <div class="separator separator-dashed my-5"></div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {

            let fetching = false;

            const config = {
                api : "{{ route("user.getUserActivities", ["user" => $user->id]) }}",
                params : {
                    page : 1
                },
            };

            const timeline_template = document.getElementById('timeline-template');
            var date_section_template = timeline_template.querySelectorAll('.date_section')[0];
            date_section_template = date_section_template.cloneNode(true);
            var activity_item_template = date_section_template.querySelectorAll('.activity-item')[0];
            activity_item_template = activity_item_template.cloneNode(true);

            const spinner = `
                <div class="spinner-tr d-flex align-items-center justify-content-center">
                    <span class="spinner-border spinner-lg text-primary"></span>
                </div>`;

            const infiniteScroll = (selector, init = false) => {
                let target = $(selector);

                if(!!config.params.page &&
                    !fetching){
                    if(!init){
                        fetching = true;
                    }
                    target.append(spinner);

                    axios({
                            method: "get",
                            url: config.api,
                            params: config.params
                        })
                        .then(function (response) {
                            const data = response.data;

                            if(data.total > 0) {
                                listActivities(data.data, selector);

                                config.params.page = (data.last_page > data.current_page) ? data.current_page + 1 : null;

                                $('#no_activity').addClass('d-none');
                                target.addClass('h-350px');
                            } else {
                                $('#no_activity').removeClass('d-none');
                                target.removeClass('h-350px');
                            }
                        })
                        .catch(function (error) {
                            toastr.error(error.response?.data?.message || (error.response?.data?.errors !== undefined ? Object.values(error.response?.data?.errors)?.[0]?.[0] : (error.response?.data?.error || "{{ __("general.message.please_try_again") }}")));
                        })
                        .finally(function () {
                            target.find(".spinner-tr").remove();

                            if(!init) {
                                fetching = false;
                            }
                        });
                }
            }

            function listActivities(data, outputSelector)
            {
                // loop data
                $.each(data, function(index, activity) {
                    var date = $('#activities-list').find("div[data-date='"+ activity.created_at_date +"']");

                    // Clone template node
                    const newItemTemplate = activity_item_template.cloneNode(true);
                    Object.keys(activity).forEach(key => {
                        const cell = newItemTemplate.querySelector(`.activity_${key}`);
                        if (cell) {
                            cell.innerHTML = activity[key];
                        }
                    });

                    if(date.length > 0) {
                        // append data into section
                        date.find('.activities_section').append(newItemTemplate);
                    } else {
                        // create section
                        const newDateSectionTemplate = date_section_template.cloneNode(true);
                        newDateSectionTemplate.setAttribute('data-date', activity.created_at_date);
                        newDateSectionTemplate.querySelector('.activities_section').innerHTML = '';
                        newDateSectionTemplate.querySelector('.activities_section').append(newItemTemplate);

                        Object.keys(activity).forEach(key => {
                            const cell = newDateSectionTemplate.querySelector(`.activity_${key}`);
                            if (cell) {
                                cell.innerHTML = activity[key];
                            }
                        });

                        $(outputSelector).append(newDateSectionTemplate);
                    }
                });
            }

            // Get initial activities list
            infiniteScroll("#activities-list", true);

            // Check if there is more data to fetch after scroll to end
            $("#activities-list").scroll(function () {
                if(this.scrollTop + this.offsetHeight >= this.scrollHeight - 1){
                    infiniteScroll("#activities-list");
                }
            });

        })
    </script>
@endpush
