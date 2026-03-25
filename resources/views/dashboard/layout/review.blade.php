@extends('master')

@section('title', 'Reviews & Feedback')
<link rel="stylesheet" href="{{ asset('dashboard/assets/css/review.css') }}">
@section('content')
    <div class="main-container">

        <!-- Main Content -->
        <main class="content">

            <!-- Stats Section -->
            <section class="stats-section">
                <div class="stat-card main-stat">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value">{{ $avaregeRating}}</span>
                        <span class="stat-label">Average Rating</span>
                    </div>
                    <div class="review-rating stars">
                        @for ($i = 0; $i < 5; $i++)
                            @if ($i < floor($avaregeRating))
                                ★
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value">{{ $totalReviews }}</span>
                        <span class="stat-label">Total Reviews</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-thumbs-up"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value">{{ $positiveReviews }}</span>
                        <span class="stat-label">Positive Reviews</span>
                    </div>
                </div>
                <div class="stat-card">

                    <div class="stat-icon blue">
                        <i class="fas fa-comment-alt"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value">{{ $totalcomments }}</span>
                        <span class="stat-label">Total Comments</span>
                    </div>
                </div>
            </section>

            <!-- Reviews Section -->
            <section class="reviews-section">

                <h2>List of Reviews</h2>
                <!-- Filter Form -->
                <form method="GET" action="{{ route('reviews.trainer', $trainerId) }}" class="reviews-filter">
                    <div class="filter-row">
                        <!-- Filter by Username -->
                        <div class="filter-item">
                            <label for="username">Username:</label>
                            <input type="text" name="username" id="username" value="{{ request('username') }}"
                                placeholder="Search by name">
                        </div>

                        <!-- Filter by Rating -->
                        <div class="filter-item">
                            <label for="star_filter">Rating:</label>
                            <select name="star" id="star_filter">
                                <option value="">All Ratings</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ request('star') == $i ? 'selected' : '' }}>
                                        {{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Filter by Date -->
                        <div class="filter-item">
                            <label for="date_from">From:</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="filter-item">
                            <label for="date_to">To:</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}">
                        </div>

                        <!-- Filter by Comment -->
                        <div class="filter-item">
                            <label for="comment">Keyword:</label>
                            <input type="text" name="comment" id="comment" value="{{ request('comment') }}"
                                placeholder="Search in comments">
                        </div>

                        <div class="filter-item">
                            <button type="submit">Filter</button>
                            <a href="{{ route('reviews.trainer', $trainerId) }}" class="btn-reset">Reset</a>
                        </div>
                    </div>
                </form>
                @forelse($reviews as $review)
                    <!-- Review Card -->
                    <div class="review-card" id="review-{{ $review->id }}">
                        <div class="review-header">
                            <div class="review-user">
                                <div class="reviewer-avatar">
                                    @php
                                        $nameParts = preg_split('/\s+/', trim($review->user->name));
                                        $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1));
                                        if (count($nameParts) > 1) {
                                            $initials .= strtoupper(substr($nameParts[1], 0, 1));
                                        }
                                    @endphp

                                    @if (!empty($review->user->avatar))
                                        <img src="{{ asset('storage/' . $review->user->avatar) }}"
                                            alt="{{ $review->user->name }}" />
                                    @else
                                        {{ $initials ?: 'U' }}
                                    @endif
                                </div>
                                <span class="username">{{ $review->user->name }}</span>
                            </div>
                            <div class="review-rating stars">
                                @if ($review->star_array['full'] > 0)
                                    @foreach (range(1, $review->star_array['full']) as $i)
                                        <i class="fas fa-star filled"></i>
                                    @endforeach
                                @endif

                                @if ($review->star_array['half'])
                                    <i class="fas fa-star-half-alt"></i>
                                @endif

                                @if ($review->star_array['empty'] > 0)
                                    @foreach (range(1, $review->star_array['empty']) as $i)
                                        <i class="far fa-star"></i>
                                    @endforeach
                                @endif

                                <span class="rating-value">{{ $review->rating }}</span>
                            </div>
                        </div>

                        <div class="review-body">
                            <p>{{ $review->comment }}</p>
                        </div>

                        <!-- مكان الرد -->
                        <div class="review-reply" id="reply-for-{{ $review->id }}"
                            style="margin-top:10px; padding-left:20px; border-left:2px solid #ddd;">
                            @if ($review->reply)
                                <strong>ME:</strong>
                                <p>{{ $review->reply }}</p>
                            @endif
                        </div>

                        <div class="review-footer">
                            <span class="review-date">{{ $review->created_at->format('F j, Y') }}</span>
                            <button class="btn-reply"
                                onclick="openReplyModal({{ $review->id }}, '{{ addslashes($review->comment) }}')">
                                Reply
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="no-reviews">
                        <i class="fas fa-inbox"></i>
                        <p>No reviews yet</p>
                    </div>
                @endforelse
                <!-- مودال عام للرد -->
                <div id="replyModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeReplyModal()">&times;</span>
                        <h3>Reply to Review</h3>
                        <p id="modalReviewText"></p>

                        <textarea id="replyText" placeholder="Write your reply..." rows="4"
                            style="width:100%; padding:8px; margin-top:10px;"></textarea>
                        <button class="modal-btn" onclick="sendReply()">Send Reply</button>
                    </div>
                </div>
                <!-- يمكنك تكرار نفس الكارت هنا لمراجعات أخرى -->
            </section>


        </main>
    </div>
    <script>
        let currentReviewId = null;

        function openReplyModal(reviewId, commentText) {
            currentReviewId = reviewId;
            document.getElementById('modalReviewText').innerText = commentText;
            document.getElementById('replyText').value = ''; // تفريغ الحقل
            document.getElementById('replyModal').style.display = 'block';
        }

        function closeReplyModal() {
            document.getElementById('replyModal').style.display = 'none';
        }

        // إغلاق عند النقر خارج المودال
        window.onclick = function(event) {
            let modal = document.getElementById('replyModal');
            if (event.target == modal) {
                closeReplyModal();
            }
        }

        // إرسال الرد عبر AJAX
        function sendReply() {
            let reply = document.getElementById('replyText').value.trim();
            if (!reply) {
                alert('Please write a reply!');
                return;
            }

            fetch(`/reviews/${currentReviewId}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // فقط داخل Blade
                    },
                    body: JSON.stringify({
                        reply
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Reply sent successfully!');
                        closeReplyModal();

                        // تحديث الرد مباشرة أسفل التعليق
                        const replyDiv = document.getElementById(`reply-for-${currentReviewId}`);
                        replyDiv.innerHTML = `<strong>ME:</strong><p>${reply}</p>`;
                    } else {
                        console.error(data.error);
                        alert('Failed to send reply. Check console for error.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error sending reply.');
                });
        }
    </script>
@endsection
