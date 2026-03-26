// API Configuration
const API_CONFIG = {
    // In production, replace with actual API endpoints
    baseUrl: 'https://api.example.com',
    reviewsEndpoint: '/api/v1/trainer/reviews',
    replyEndpoint: '/api/v1/trainer/reviews/reply'
};

// Mock Data for demonstration (simulating API response)
const mockReviews = [
    {
        id: 1,
        traineeId: 101,
        traineeName: 'سارة أحمد',
        sessionTitle: 'تعلم Python للمبتدئين',
        sessionDate: '2026-03-20',
        rating: 5,
        comment: 'ممتاز! شرح واضح جداً والمعلم صبور. أستفدت كثيراً من الجلسة.',
        hasReply: true,
        reply: 'شكراً لكِ سارة، سعيد جداً بأن الجلسة أفادتك. في انتظار جلستك القادمة!'
    },

];

// Global state
let reviews = [];
let currentReviewId = null;

// DOM Elements
const reviewsList = document.getElementById('reviewsList');
const noReviews = document.getElementById('noReviews');
const loadingOverlay = document.getElementById('loadingOverlay');
const searchInput = document.getElementById('searchInput');
const sortSelect = document.getElementById('sortSelect');
const filterSelect = document.getElementById('filterSelect');
const replyModal = document.getElementById('replyModal');
const replyText = document.getElementById('replyText');
const originalCommentText = document.getElementById('originalCommentText');

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    fetchReviews();
    setupEventListeners();
});

// Setup Event Listeners
function setupEventListeners() {
    searchInput.addEventListener('input', handleSearch);
    sortSelect.addEventListener('change', handleSort);
    filterSelect.addEventListener('change', handleFilter);
}

// API Call - Fetch Reviews
async function fetchReviews() {
    showLoading(true);

    try {
        // In production, use actual API call:
        // const response = await fetch(`${API_CONFIG.baseUrl}${API_CONFIG.reviewsEndpoint}`, {
        //     headers: {
        //         'Authorization': `Bearer ${getAuthToken()}`,
        //         'Content-Type': 'application/json'
        //     }
        // });
        // const data = await response.json();
        // reviews = data.reviews;

        // For demo, using mock data with simulated delay
        await simulateApiDelay();
        reviews = mockReviews;

        updateStats();
        renderReviews(reviews);
    } catch (error) {
        console.error('Error fetching reviews:', error);
        showError('حدث خطأ في جلب التقييمات. يرجى المحاولة لاحقاً.');
    } finally {
        showLoading(false);
    }
}

// Simulate API delay
function simulateApiDelay() {
    return new Promise(resolve => setTimeout(resolve, 800));
}

// Update Statistics
function updateStats() {
    const totalReviews = reviews.length;
    const avgRating = totalReviews > 0
        ? (reviews.reduce((sum, r) => sum + r.rating, 0) / totalReviews).toFixed(1)
        : 0;
    const positiveReviews = reviews.filter(r => r.rating >= 4).length;
    const totalSessions = totalReviews + 15; // Mock additional sessions

    document.getElementById('avgRating').textContent = avgRating;
    document.getElementById('totalReviews').textContent = totalReviews;
    document.getElementById('positiveReviews').textContent = positiveReviews;
    document.getElementById('totalSessions').textContent = totalSessions;
}

// Render Reviews
function renderReviews(reviewsToRender) {
    reviewsList.innerHTML = '';

    if (reviewsToRender.length === 0) {
        noReviews.style.display = 'block';
        return;
    }

    noReviews.style.display = 'none';

    reviewsToRender.forEach((review, index) => {
        const reviewCard = createReviewCard(review, index);
        reviewsList.appendChild(reviewCard);
    });
}

// Create Review Card HTML
function createReviewCard(review, index) {
    const card = document.createElement('div');
    card.className = 'review-card';
    card.style.animationDelay = `${index * 0.1}s`;

    const initials = getInitials(review.traineeName);
    const stars = generateStars(review.rating);
    const dateFormatted = formatDate(review.sessionDate);

    card.innerHTML = `
        <div class="review-header">
            <div class="reviewer-info">
                <div class="reviewer-avatar">${initials}</div>
                <div class="reviewer-details">
                    <h4>${review.traineeName}</h4>
                    <span class="session-date">${dateFormatted}</span>
                </div>
            </div>
            <div class="review-rating">
                <div class="stars">${stars}</div>
                <span class="rating-number">${review.rating}/5</span>
            </div>
        </div>
        <div class="review-comment">
            <p>${review.comment}</p>
        </div>
        <div class="review-footer">
            <div class="review-session">
                <i class="fas fa-video"></i>
                <span>${review.sessionTitle}</span>
            </div>
            ${!review.hasReply ? `
                <button class="reply-btn" onclick="openReplyModal(${review.id})">
                    <i class="fas fa-reply"></i>
                    <span>الرد على التعليق</span>
                </button>
            ` : '<span style="color: var(--success-color); font-size: 13px;"><i class="fas fa-check-circle"></i> تم الرد</span>'}
        </div>
        ${review.hasReply && review.reply ? `
            <div class="review-reply">
                <h5><i class="fas fa-check"></i> ردك:</h5>
                <p>${review.reply}</p>
            </div>
        ` : ''}
    `;

    return card;
}

// Generate Star Icons
function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star filled"></i>';
        } else {
            stars += '<i class="fas fa-star"></i>';
        }
    }
    return stars;
}

// Get Name Initials
function getInitials(name) {
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return parts[0][0] + parts[1][0];
    }
    return name.substring(0, 2);
}

// Format Date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('ar-SA', options);
}

// Search Handler
function handleSearch() {
    const searchTerm = searchInput.value.toLowerCase();
    const filtered = reviews.filter(review =>
        review.traineeName.toLowerCase().includes(searchTerm) ||
        review.comment.toLowerCase().includes(searchTerm) ||
        review.sessionTitle.toLowerCase().includes(searchTerm)
    );
    applyFiltersAndSort(filtered);
}

// Sort Handler
function handleSort() {
    applyFiltersAndSort(reviews);
}

// Filter Handler
function handleFilter() {
    applyFiltersAndSort(reviews);
}

// Apply Filters and Sort
function applyFiltersAndSort(reviewsArray) {
    let result = [...reviewsArray];

    // Apply search filter
    const searchTerm = searchInput.value.toLowerCase();
    if (searchTerm) {
        result = result.filter(review =>
            review.traineeName.toLowerCase().includes(searchTerm) ||
            review.comment.toLowerCase().includes(searchTerm) ||
            review.sessionTitle.toLowerCase().includes(searchTerm)
        );
    }

    // Apply rating filter
    const filterValue = filterSelect.value;
    if (filterValue !== 'all') {
        result = result.filter(review => review.rating === parseInt(filterValue));
    }

    // Apply sorting
    const sortValue = sortSelect.value;
    switch (sortValue) {
        case 'date-desc':
            result.sort((a, b) => new Date(b.sessionDate) - new Date(a.sessionDate));
            break;
        case 'date-asc':
            result.sort((a, b) => new Date(a.sessionDate) - new Date(b.sessionDate));
            break;
        case 'rating-desc':
            result.sort((a, b) => b.rating - a.rating);
            break;
        case 'rating-asc':
            result.sort((a, b) => a.rating - b.rating);
            break;
    }

    renderReviews(result);
}

// Open Reply Modal
function openReplyModal(reviewId) {
    const review = reviews.find(r => r.id === reviewId);
    if (!review) return;

    currentReviewId = reviewId;
    originalCommentText.textContent = review.comment;
    replyText.value = '';
    replyModal.classList.add('show');
}

// Close Modal
function closeModal() {
    replyModal.classList.remove('show');
    currentReviewId = null;
}

// Submit Reply
async function submitReply() {
    const reply = replyText.value.trim();

    if (!reply) {
        showError('الرجاء كتابة رد قبل الإرسال');
        return;
    }

    showLoading(true);

    try {
        // In production, use actual API call:
        // const response = await fetch(`${API_CONFIG.baseUrl}${API_CONFIG.replyEndpoint}`, {
        //     method: 'POST',
        //     headers: {
        //         'Authorization': `Bearer ${getAuthToken()}`,
        //         'Content-Type': 'application/json'
        //     },
        //     body: JSON.stringify({
        //         reviewId: currentReviewId,
        //         reply: reply
        //     })
        // });

        // Simulate API call
        await simulateApiDelay();

        // Update local data
        const reviewIndex = reviews.findIndex(r => r.id === currentReviewId);
        if (reviewIndex !== -1) {
            reviews[reviewIndex].hasReply = true;
            reviews[reviewIndex].reply = reply;
        }

        renderReviews(reviews);
        closeModal();
        showSuccess('تم إرسال الرد بنجاح');
    } catch (error) {
        console.error('Error submitting reply:', error);
        showError('حدث خطأ في إرسال الرد. يرجى المحاولة لاحقاً.');
    } finally {
        showLoading(false);
    }
}

// Show Loading
function showLoading(show) {
    if (show) {
        loadingOverlay.classList.add('show');
    } else {
        loadingOverlay.classList.remove('show');
    }
}

// Show Error Message
function showError(message) {
    // You can implement a toast/notification system here
    alert(message);
}

// Show Success Message
function showSuccess(message) {
    // You can implement a toast/notification system here
    alert(message);
}

// Close modal when clicking outside
replyModal.addEventListener('click', (e) => {
    if (e.target === replyModal) {
        closeModal();
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && replyModal.classList.contains('show')) {
        closeModal();
    }
});
