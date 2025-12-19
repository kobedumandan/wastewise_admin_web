@extends('layouts.admin')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    /* Calendar Custom Styling */
    .fc {
        font-family: 'Poppins', sans-serif;
    }

    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #dee2e6;
    }

    .fc-header-toolbar {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: linear-gradient(135deg, #0f7024 0%, #155749 50%, #156725 100%);
        border-radius: 10px;
        color: white;
    }

    .fc-button {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        color: white !important;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .fc-button:hover {
        background-color: rgba(255, 255, 255, 0.2) !important;
        transform: translateY(-2px);
    }

    .fc-button-active {
        background-color: #a59f19 !important;
        border-color: #a59f19 !important;
    }

    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 600 !important;
        color: white !important;
    }

    .fc-daygrid-day {
        background-color: #ffffff;
        transition: all 0.3s ease;
    }

    .fc-daygrid-day:hover {
        background-color: #f8f9fa;
    }

    .fc-day-today {
        background-color: rgba(165, 159, 25, 0.2) !important;
    }

    .fc-daygrid-day-number {
        color: #333;
        font-weight: 500;
        padding: 0.5rem;
    }

    .fc-col-header-cell {
        background-color: #f8f9fa;
        padding: 0.75rem 0;
        border-color: #dee2e6;
    }

    .fc-col-header-cell-cushion {
        color: #333;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .fc-event {
        border-radius: 5px;
        padding: 0.25rem 0.5rem;
        border: none !important;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .fc-event:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .fc-event-title {
        font-weight: 500;
    }

    .calendar-wrapper {
        background: #ffffff;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid #dee2e6;
        width: 100%;
        overflow-x: auto;
    }
    
    /* Make calendar responsive to sidebar expansion */
    body.sidebar-expanded .calendar-wrapper {
        padding: 1.5rem;
    }
    
    body.sidebar-expanded .fc {
        font-size: 0.9em;
    }
    
    body.sidebar-expanded .fc-col-header-cell-cushion,
    body.sidebar-expanded .fc-daygrid-day-number {
        font-size: 0.8rem;
        padding: 0.3rem;
    }
    
    body.sidebar-expanded .fc-event {
        font-size: 0.75rem;
        padding: 0.2rem 0.4rem;
    }
    
    body.sidebar-expanded .fc-toolbar-title {
        font-size: 1.2rem !important;
    }
    
    body.sidebar-expanded .fc-button {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }
    
    .fc {
        background-color: #ffffff;
    }
    
    .fc-theme-standard {
        background-color: #ffffff;
    }
    
    .fc-scrollgrid {
        border-color: #dee2e6;
    }
    
    .fc-daygrid-body {
        background-color: #ffffff;
    }

    .fc-daygrid-event {
        white-space: normal;
        word-wrap: break-word;
    }

    /* Custom scrollbar for calendar */
    .fc-scroller::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .fc-scroller::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 4px;
    }

    .fc-scroller::-webkit-scrollbar-thumb {
        background: #156725;
        border-radius: 4px;
    }

    .fc-scroller::-webkit-scrollbar-thumb:hover {
        background: #0d4d1a;
    }
    
    /* Ensure all calendar text is visible */
    .fc-daygrid-day-top {
        color: #333;
    }
    
    .fc-day-other .fc-daygrid-day-top {
        color: #999;
    }
    
    .fc-more-link {
        color: #156725;
    }
    
    .fc-popover {
        background-color: #ffffff;
        border-color: #dee2e6;
        color: #333;
    }
    
    .fc-popover-header {
        background-color: #f8f9fa;
        color: #333;
        border-bottom-color: #dee2e6;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <p class="text-muted mb-4">Manage collection schedules for different puroks</p>

        <!-- Scheduled Now Card -->
        <div class="row mt-4 mb-4">
            <div class="col-md-4">
                <div class="card h-100" style="border: 1px solid #dee2e6; border-radius: 14px; position: relative; overflow: hidden; background-image: url('{{ asset('backgrounds/total_sacks.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat;">
                    <div class="card-body d-flex align-items-center justify-content-end" style="position: relative; z-index: 2;">
                        <div class="card-content" style="position: relative; z-index: 2;">
                            <p class="card-title" style="color: #333; font-weight: 400; font-size: 15px; height: 2px;">Scheduled Now</p>
                            <p class="card-value ms-auto" style="color: #333; font-weight: 600; font-size: 56px;">
                                <strong>
                                    @if($todayPuroks->count() > 0)
                                        {{ $todayPuroks->join(', ') }}
                                    @else
                                        None
                                    @endif
                                </strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(!$collectionStarted && $todayScheduleKey && $todayPuroks->count() > 0)
            <div class="col-md-4">
                <div class="card h-100" style="border: 1px solid #dee2e6; border-radius: 14px; background-color: #f8f9fa; cursor: pointer; transition: all 0.3s ease;" id="start-collection-card">
                    <div class="card-body d-flex align-items-center" style="padding: 1.5rem;">
                        <div class="card-content d-flex align-items-center gap-3" style="width: 100%;">
                            <i class="bi bi-play-circle" style="font-size: 32px; color: #6c757d;"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="color: #495057; font-weight: 500; font-size: 16px; margin: 0;">
                                    Start Collection
                                </h6>
                                <p class="mb-0" style="color: #6c757d; font-size: 12px;">
                                    Begin today's collection
                                </p>
                            </div>
                            <i class="bi bi-chevron-right" style="font-size: 16px; color: #adb5bd;"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            @if($collectionStarted && $todayPuroks->count() > 0)
            <div class="col-md-4">
                <div class="card h-100" style="border: 1px solid #d4edda; border-radius: 14px; background-color: #f0f9f4; transition: all 0.3s ease;" id="collection-started-card">
                    <div class="card-body d-flex align-items-center" style="padding: 1.5rem;">
                        <div class="card-content d-flex align-items-center gap-3" style="width: 100%;">
                            <i class="bi bi-check-circle-fill" style="font-size: 32px; color: #28a745;"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="color: #155724; font-weight: 500; font-size: 16px; margin: 0;">
                                    Collection Started
                                </h6>
                                <p class="mb-0" style="color: #6c757d; font-size: 12px;">
                                    Route is now active
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Legend -->
        <div class="card mb-4" style="border: 1px solid #dee2e6; border-radius: 14px;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <p class="card-title mb-3" style="color: #333; font-weight: 600; font-size: 17px;">
                    <i class="bi bi-info-circle me-2"></i>Schedule Status Legend
</p>
                <div class="d-flex flex-wrap gap-4 align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 18px; height: 18px; background-color: #ffc107; border-radius: 4px; border: 1px solid #ffc107;"></div>
                        <span style="color: #333; font-size: 14px;">Current Schedule</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 18px; height: 18px; background-color: #156725; border-radius: 4px; border: 1px solid #156725;"></div>
                        <span style="color: #333; font-size: 14px;">Upcoming Schedule</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 18px; height: 18px; background-color: #6c757d; border-radius: 4px; border: 1px solid #6c757d;"></div>
                        <span style="color: #333; font-size: 14px;">Completed Schedule</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="calendar-wrapper">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Modal for adding schedule -->
    <div class="modal fade" id="scheduleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #ffffff; border: 1px solid #dee2e6;">
                <div class="modal-header border-0" style="border-bottom: 1px solid #dee2e6 !important;">
                    <h5 class="modal-title" style="color: #333;">
                        <i class="bi bi-calendar-plus me-2"></i>
                        Add Collection Schedule
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="scheduleForm">
                        <input type="hidden" id="date_schedule" name="date_schedule">
                        <div class="mb-3">
                            <label for="purok_name" class="form-label" style="color: #333;">
                                <i class="bi bi-geo-alt me-2"></i>
                                Purok Name
                            </label>
                            <select class="form-control" id="purok_id" name="purok_id" required
                                    style="background: #ffffff; border: 1px solid #dee2e6; color: #333;">
                                <option value="">Select Purok</option>
                                @foreach($puroks as $purok)
                                    <option value="{{ $purok->key }}">{{ $purok->purok_name ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="lead_collector_id" class="form-label" style="color: #333;">
                                <i class="bi bi-person-badge me-2"></i>
                                Lead Collector
                            </label>
                            <select class="form-control" id="lead_collector_id" name="lead_collector_id" required
                                    style="background: #ffffff; border: 1px solid #dee2e6; color: #333;">
                                <option value="">Select Lead Collector</option>
                                @foreach($collectors as $collector)
                                    <option value="{{ $collector->key }}">
                                        {{ ($collector->coll_fname ?? '') . ' ' . ($collector->coll_lname ?? '') }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Select the collector who will lead this collection route</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="color: #333;">
                                <i class="bi bi-calendar-event me-2"></i>
                                Selected Date
                            </label>
                            <input type="text" class="form-control" id="display_date" readonly
                                   style="background: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d;">
                        </div>
                        <button type="submit" class="btn w-100" style="background:rgb(76, 186, 98); color: white; font-weight: 600;">
                            <i class="bi bi-check-circle me-2"></i>
                            Save Schedule
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
        var selectedDate = null;
        var existingEvents = []; // Store events to check against

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            themeSystem: 'standard',
            firstDay: 1, // Start week on Monday
            dayMaxEvents: true,
            moreLinkClick: 'popover',
            dateClick: function(info) {
                const clickedDate = info.dateStr;
                
                // Check if the clicked date is in the past
                const clickedDateObj = new Date(clickedDate);
                clickedDateObj.setHours(0, 0, 0, 0);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (clickedDateObj < today) {
                    // Show warning message for past dates
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alertDiv.style.zIndex = '9999';
                    alertDiv.innerHTML = `
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Cannot add schedules for past dates. Please select today or a future date.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    setTimeout(() => alertDiv.remove(), 4000);
                    return;
                }
                
                // Check if there's already a schedule for this date
                const hasSchedule = existingEvents.some(event => {
                    const eventDate = event.start.split('T')[0]; // Get date part only
                    return eventDate === clickedDate;
                });

                if (hasSchedule) {
                    // Show warning message instead of opening modal
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alertDiv.style.zIndex = '9999';
                    alertDiv.innerHTML = `
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        A schedule already exists for this date. Click on the event to delete it.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    setTimeout(() => alertDiv.remove(), 4000);
                    return;
                }

                selectedDate = clickedDate;
                document.getElementById('date_schedule').value = selectedDate;
                
                // Format date for display
                const dateObj = new Date(selectedDate);
                const formattedDate = dateObj.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                document.getElementById('display_date').value = formattedDate;
                
                scheduleModal.show();
            },
            eventClick: function(info) {
                // Check if schedule is in the past
                const scheduleDate = new Date(info.event.start);
                scheduleDate.setHours(0, 0, 0, 0);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                const isPast = scheduleDate < today;
                
                if (isPast) {
                    // Show message that past schedules cannot be deleted
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alertDiv.style.zIndex = '9999';
                    alertDiv.innerHTML = `
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Cannot delete completed schedules. This schedule date has already passed.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    setTimeout(() => alertDiv.remove(), 4000);
                    return;
                }
                
                if (confirm(`Delete schedule for ${info.event.title} on ${info.event.start.toLocaleDateString()}?`)) {
                    fetch('/delete-purok', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            schedule_key: info.event.id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            info.event.remove();
                            // Remove from existingEvents array
                            existingEvents = existingEvents.filter(event => event.id !== info.event.id);
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                            alertDiv.style.zIndex = '9999';
                            alertDiv.innerHTML = `
                                <i class="bi bi-check-circle me-2"></i>
                                Schedule deleted successfully
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            document.body.appendChild(alertDiv);
                            setTimeout(() => alertDiv.remove(), 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting schedule');
                    });
                }
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch('/schedules')
                    .then(response => response.json())
                    .then(data => {
                        const today = new Date();
                        today.setHours(0, 0, 0, 0); // Set to start of today for accurate comparison
                        
                        const events = data.map(schedule => {
                            const scheduleDate = new Date(schedule.date_schedule);
                            scheduleDate.setHours(0, 0, 0, 0); // Set to start of day for accurate comparison
                            
                            // Check if schedule is today, past, or future
                            const isToday = scheduleDate.getTime() === today.getTime();
                            const isPast = scheduleDate < today;
                            
                            let backgroundColor, borderColor;
                            if (isToday) {
                                // Yellow for current/today's schedule
                                backgroundColor = '#ffc107';
                                borderColor = '#ffc107';
                            } else if (isPast) {
                                // Grey for past/completed schedules
                                backgroundColor = '#6c757d';
                                borderColor = '#6c757d';
                            } else {
                                // Green for upcoming schedules
                                backgroundColor = '#156725';
                                borderColor = '#156725';
                            }
                            
                            return {
                                id: schedule.key,
                                title: schedule.purok_name,
                                start: schedule.date_schedule,
                                backgroundColor: backgroundColor,
                                borderColor: borderColor,
                                textColor: '#ffffff'
                            };
                        });
                        // Store events for checking in dateClick
                        existingEvents = events;
                        successCallback(events);
                    })
                    .catch(error => {
                        console.error('Error fetching schedules:', error);
                        failureCallback(error);
                    });
            },
            height: 'auto',
            aspectRatio: 1.8
        });

        calendar.render();

        // Adjust calendar size when sidebar toggles
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    // Wait for transition to complete before resizing
                    setTimeout(() => {
                        calendar.updateSize();
                    }, 350);
                }
            });
        });
        
        // Observe body class changes for sidebar expansion
        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['class']
        });
        
        // Also listen to window resize events
        window.addEventListener('resize', function() {
            calendar.updateSize();
        });

        // Handle form submission
        document.getElementById('scheduleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                date_schedule: document.getElementById('date_schedule').value,
                purok_id: document.getElementById('purok_id').value,
                lead_collector_id: document.getElementById('lead_collector_id').value
            };

            fetch('/save-purok', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    scheduleModal.hide();
                    document.getElementById('scheduleForm').reset();
                    calendar.refetchEvents();
                    
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alertDiv.style.zIndex = '9999';
                    alertDiv.innerHTML = `
                        <i class="bi bi-check-circle me-2"></i>
                        Schedule saved successfully
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    setTimeout(() => alertDiv.remove(), 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving schedule');
            });
        });

        // Close modal handler
        document.getElementById('scheduleModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('scheduleForm').reset();
        });

        // Handle start collection card click
        const startCollectionCard = document.getElementById('start-collection-card');
        if (startCollectionCard) {
            startCollectionCard.addEventListener('click', function() {
                if (confirm('Start collection for today? This will mark the collection as started.')) {
                    const scheduleKey = '{{ $todayScheduleKey }}';
                    
                    fetch('/start-collection', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            schedule_key: scheduleKey
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hide the start collection card
                            const startCardContainer = startCollectionCard.closest('.col-md-4');
                            startCardContainer.style.display = 'none';
                            
                            // Create and show "Collection Started" feedback card
                            const feedbackCard = document.createElement('div');
                            feedbackCard.className = 'col-md-4';
                            feedbackCard.id = 'collection-started-feedback';
                            feedbackCard.innerHTML = `
                                <div class="card h-100" style="border: 1px solid #d4edda; border-radius: 14px; background-color: #f0f9f4; transition: all 0.3s ease;">
                                    <div class="card-body d-flex align-items-center" style="padding: 1.5rem;">
                                        <div class="card-content d-flex align-items-center gap-3" style="width: 100%;">
                                            <i class="bi bi-check-circle-fill" style="font-size: 32px; color: #28a745;"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1" style="color: #155724; font-weight: 500; font-size: 16px; margin: 0;">
                                                    Collection Started
                                                </h6>
                                                <p class="mb-0" style="color: #6c757d; font-size: 12px;">
                                                    Route is now active
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            // Insert the feedback card after the start card container's parent row
                            startCardContainer.parentElement.insertBefore(feedbackCard, startCardContainer.nextSibling);
                            
                            // Add fade-in animation
                            feedbackCard.style.opacity = '0';
                            feedbackCard.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                feedbackCard.style.transition = 'all 0.5s ease';
                                feedbackCard.style.opacity = '1';
                                feedbackCard.style.transform = 'translateY(0)';
                            }, 10);
                        } else {
                            alert('Failed to start collection. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error starting collection');
                    });
                }
            });
            
            // Add subtle hover effect
            startCollectionCard.addEventListener('mouseenter', function() {
                this.style.borderColor = '#156725';
                this.style.backgroundColor = '#f0f5f1';
                const icon = this.querySelector('.bi-play-circle');
                if (icon) icon.style.color = '#156725';
            });
            
            startCollectionCard.addEventListener('mouseleave', function() {
                this.style.borderColor = '#dee2e6';
                this.style.backgroundColor = '#f8f9fa';
                const icon = this.querySelector('.bi-play-circle');
                if (icon) icon.style.color = '#6c757d';
            });
        }
    });
</script>
@endpush
