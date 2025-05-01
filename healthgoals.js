document.addEventListener('DOMContentLoaded', function() {
    // Initialize the application
    initHealthGoals();
});

/**
 * Main initialization function
 */
function initHealthGoals() {
    // Set up current date and default values
    setupDates();
    
    // Set up goal type selection
    setupGoalTypes();
    
    // Set up form validation
    setupFormValidation();
    
    // Set up event listeners
    setupEventListeners();
    
    // Set up progress tracking
    setupProgressTracking();
    
    // Load any existing goals from storage
    loadGoalsFromStorage();
    
    // Set up notifications
    setupNotificationPreferences();
    
    // Set up motivational quotes
    setupMotivationalQuotes();
}

/**
 * Set up date fields with default values
 */
function setupDates() {
    // Format and display current date
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    const today = new Date();
    document.getElementById('current-date').textContent = today.toLocaleDateString('en-US', options);
    
    // Set default dates (today and 1 month from today)
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    
    startDateInput.valueAsDate = today;
    startDateInput.min = formatDateForInput(today);
    
    const defaultEndDate = new Date(today);
    defaultEndDate.setMonth(today.getMonth() + 1);
    endDateInput.valueAsDate = defaultEndDate;
    endDateInput.min = formatDateForInput(today);
    
    // Ensure end date is always after start date
    startDateInput.addEventListener('change', function() {
        const startDate = new Date(this.value);
        endDateInput.min = formatDateForInput(startDate);
        
        // If current end date is before new start date, adjust it
        const currentEndDate = new Date(endDateInput.value);
        if (currentEndDate < startDate) {
            const newEndDate = new Date(startDate);
            newEndDate.setMonth(startDate.getMonth() + 1);
            endDateInput.valueAsDate = newEndDate;
        }
        
        // Calculate and update duration
        updateDurationDisplay();
    });
    
    endDateInput.addEventListener('change', updateDurationDisplay);
}

/**
 * Update duration display between start and end dates
 */
function updateDurationDisplay() {
    const startDate = new Date(document.getElementById('start-date').value);
    const endDate = new Date(document.getElementById('end-date').value);
    
    if (startDate && endDate && startDate < endDate) {
        const durationInDays = Math.round((endDate - startDate) / (1000 * 60 * 60 * 24));
        const durationInWeeks = Math.round(durationInDays / 7);
        
        let durationText = `${durationInDays} days`;
        if (durationInWeeks >= 2) {
            durationText = `${durationInWeeks} weeks (${durationInDays} days)`;
        }
        
        // Create or update duration display
        let durationElement = document.getElementById('duration-display');
        if (!durationElement) {
            durationElement = document.createElement('div');
            durationElement.id = 'duration-display';
            durationElement.className = 'duration-info';
            document.querySelector('.form-row').after(durationElement);
        }
        
        durationElement.innerHTML = `
            <i class="fas fa-calendar-alt"></i>
            <span>Goal duration: ${durationText}</span>
        `;
    }
}

/**
 * Set up goal type selection and form field updates
 */
function setupGoalTypes() {
    const goalTypes = document.querySelectorAll('.goal-type');
    const defaultType = 'weight';  // Matches the active type in HTML
    
    // Add click handlers for each goal type
    goalTypes.forEach(type => {
        type.addEventListener('click', function() {
            // Update active state
            goalTypes.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Update form fields based on selected type
            updateFormFields(this.dataset.type);
            
            // Show motivational quote for this goal type
            showMotivationalQuote(this.dataset.type);
        });
    });
    
    // Initialize form fields for default type
    updateFormFields(defaultType);
}

/**
 * Update form fields based on selected goal type
 */
function updateFormFields(type) {
    const unitSelect = document.getElementById('target-unit');
    const titleInput = document.getElementById('goal-title');
    const targetValueInput = document.getElementById('target-value');
    const descriptionInput = document.getElementById('goal-description');
    
    // Clear any validation errors
    titleInput.classList.remove('error');
    targetValueInput.classList.remove('error');
    
    // Default template for description
    let descriptionTemplate = "I want to achieve this goal because...";
    
    switch(type) {
        case 'weight':
            titleInput.placeholder = "e.g. Lose 5kg in 3 months";
            unitSelect.innerHTML = `
                <option value="kg">kg</option>
                <option value="lbs">lbs</option>
                <option value="bmi">BMI points</option>
            `;
            targetValueInput.placeholder = "e.g. 65";
            descriptionTemplate = "I want to manage my weight because... (improve health, feel better, etc.)";
            break;
        case 'activity':
            titleInput.placeholder = "e.g. Walk 10,000 steps daily";
            unitSelect.innerHTML = `
                <option value="steps">steps</option>
                <option value="minutes">minutes</option>
                <option value="hours">hours</option>
                <option value="days">days per week</option>
                <option value="miles">miles</option>
                <option value="km">kilometers</option>
            `;
            targetValueInput.placeholder = "e.g. 10000";
            descriptionTemplate = "I want to be more active because... (increase energy, reduce stress, etc.)";
            break;
        case 'nutrition':
            titleInput.placeholder = "e.g. Eat 5 servings of vegetables daily";
            unitSelect.innerHTML = `
                <option value="servings">servings</option>
                <option value="times">times</option>
                <option value="days">days</option>
                <option value="cups">cups</option>
                <option value="oz">ounces</option>
                <option value="ml">milliliters</option>
            `;
            targetValueInput.placeholder = "e.g. 5";
            descriptionTemplate = "I want to improve my nutrition because... (better digestion, more energy, etc.)";
            break;
        case 'sleep':
            titleInput.placeholder = "e.g. Get 8 hours of sleep nightly";
            unitSelect.innerHTML = `
                <option value="hours">hours</option>
                <option value="days">nights per week</option>
            `;
            targetValueInput.placeholder = "e.g. 8";
            descriptionTemplate = "I want to improve my sleep because... (better focus, more energy, etc.)";
            break;
        case 'medication':
            titleInput.placeholder = "e.g. Take medication on time every day";
            unitSelect.innerHTML = `
                <option value="times">times</option>
                <option value="days">days</option>
                <option value="doses">doses</option>
                <option value="percent">% adherence</option>
            `;
            targetValueInput.placeholder = "e.g. 1";
            descriptionTemplate = "I want to improve my medication adherence because... (better health outcomes, etc.)";
            break;
        default:  // 'other'
            titleInput.placeholder = "Describe your goal...";
            unitSelect.innerHTML = `
                <option value="times">times</option>
                <option value="days">days</option>
                <option value="other">other</option>
            `;
            targetValueInput.placeholder = "Your target value";
            descriptionTemplate = "Describe your personal health goal and why it's important to you...";
    }
    
    // Set description placeholder if empty
    if (!descriptionInput.value) {
        descriptionInput.placeholder = descriptionTemplate;
    }
}

/**
 * Set up form validation
 */
function setupFormValidation() {
    const formInputs = [
        document.getElementById('goal-title'),
        document.getElementById('target-value'),
        document.getElementById('start-date'),
        document.getElementById('end-date')
    ];
    
    // Add input validation
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('error');
            const errorElement = document.getElementById(`${this.id}-error`);
            if (errorElement) errorElement.remove();
        });
    });
    
    // Add specific validation for target value
    document.getElementById('target-value').addEventListener('blur', function() {
        if (this.value && isNaN(this.value)) {
            showFieldError(this, 'Please enter a valid number');
        }
    });
}

/**
 * Show field-specific error message
 */
function showFieldError(input, message) {
    input.classList.add('error');
    
    // Remove existing error if any
    const existingError = document.getElementById(`${input.id}-error`);
    if (existingError) existingError.remove();
    
    // Create error element
    const errorElement = document.createElement('div');
    errorElement.id = `${input.id}-error`;
    errorElement.className = 'field-error';
    errorElement.textContent = message;
    
    // Insert after the input
    input.parentNode.appendChild(errorElement);
}

/**
 * Set up event listeners for buttons and form
 */
function setupEventListeners() {
    // Save goal button
    document.getElementById('saveGoalBtn').addEventListener('click', saveGoal);
    
    // Cancel button
    document.getElementById('cancelBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
            window.location.href = 'health-goals.html';
        }
    });
    
    // Add "Add Milestone" button functionality
    document.getElementById('addMilestoneBtn')?.addEventListener('click', addMilestone);
}

/**
 * Set up progress tracking features
 */
function setupProgressTracking() {
    // Add milestones section
    const milestonesSection = document.createElement('div');
    milestonesSection.className = 'form-group milestones-section';
    milestonesSection.innerHTML = `
        <label>
            Milestones 
            <button type="button" class="btn-icon" id="addMilestoneBtn">
                <i class="fas fa-plus"></i>
            </button>
        </label>
        <div class="milestones-container" id="milestonesContainer"></div>
    `;
    document.querySelector('.form-group:last-of-type').before(milestonesSection);
}

/**
 * Add a new milestone input
 */
function addMilestone() {
    const container = document.getElementById('milestonesContainer');
    const milestoneId = Date.now();
    
    const milestoneElement = document.createElement('div');
    milestoneElement.className = 'milestone';
    milestoneElement.innerHTML = `
        <input type="text" placeholder="Milestone description" class="form-control milestone-description">
        <input type="date" class="form-control milestone-date">
        <button type="button" class="btn-icon remove-milestone">
            <i class="fas fa-times"></i>
        </button>
    `;
    milestoneElement.querySelector('.remove-milestone').addEventListener('click', function() {
        container.removeChild(milestoneElement);
    });
    
    // Set default date (midpoint between start and end dates)
    const startDate = new Date(document.getElementById('start-date').value);
    const endDate = new Date(document.getElementById('end-date').value);
    if (startDate && endDate) {
        const midDate = new Date((startDate.getTime() + endDate.getTime()) / 2);
        milestoneElement.querySelector('.milestone-date').valueAsDate = midDate;
    }
    
    container.appendChild(milestoneElement);
}

/**
 * Set up notification preferences
 */
function setupNotificationPreferences() {
    const reminderSelect = document.getElementById('goal-reminder');
    
    // Add time selection for reminders
    const reminderTimeGroup = document.createElement('div');
    reminderTimeGroup.className = 'form-group reminder-time-group';
    reminderTimeGroup.style.display = 'none';
    reminderTimeGroup.innerHTML = `
        <label for="reminder-time">Reminder Time</label>
        <input type="time" id="reminder-time" class="form-control" value="09:00">
    `;
    reminderSelect.parentNode.after(reminderTimeGroup);
    
    // Show/hide time selector based on reminder selection
    reminderSelect.addEventListener('change', function() {
        reminderTimeGroup.style.display = this.value !== 'none' ? 'block' : 'none';
    });
}

/**
 * Set up motivational quotes system
 */
function setupMotivationalQuotes() {
    // Quotes database
    window.motivationalQuotes = {
        weight: [
            "Weight is just a number. Your health is the real goal!",
            "Small steps every day lead to big results over time.",
            "You don't have to be perfect, just consistent."
        ],
        activity: [
            "Movement is medicine for the body and mind.",
            "Every step counts on your journey to better health!",
            "The only bad workout is the one you didn't do."
        ],
        nutrition: [
            "Healthy eating is a form of self-respect.",
            "You are what you eat, so eat something awesome!",
            "Progress, not perfection, is what matters."
        ],
        sleep: [
            "Sleep is the best meditation. - Dalai Lama",
            "A good laugh and a long sleep are the best cures. - Irish proverb",
            "Your future depends on your dreams, so go to sleep. - Mesut Barazany"
        ],
        medication: [
            "Taking care of yourself is the most powerful way to take care of others.",
            "Your health is an investment, not an expense.",
            "Small, consistent actions lead to big health outcomes."
        ],
        other: [
            "The secret of getting ahead is getting started. - Mark Twain",
            "You're braver than you believe, and stronger than you seem. - A.A. Milne",
            "Every day is a new opportunity to improve your health."
        ]
    };
}

/**
 * Show motivational quote for goal type
 */
function showMotivationalQuote(goalType) {
    const quotes = window.motivationalQuotes[goalType] || window.motivationalQuotes.other;
    const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];
    
    let quoteElement = document.getElementById('motivational-quote');
    if (!quoteElement) {
        quoteElement = document.createElement('div');
        quoteElement.id = 'motivational-quote';
        quoteElement.className = 'motivational-quote';
        document.querySelector('.goal-form').prepend(quoteElement);
    }
    
    quoteElement.innerHTML = `
        <i class="fas fa-quote-left"></i>
        <span>${randomQuote}</span>
        <i class="fas fa-quote-right"></i>
    `;
}

/**
 * Save goal to storage
 */
function saveGoal() {
    // Get form values
    const goalType = document.querySelector('.goal-type.active').dataset.type;
    const title = document.getElementById('goal-title').value.trim();
    const targetValue = document.getElementById('target-value').value.trim();
    const unit = document.getElementById('target-unit').value;
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const description = document.getElementById('goal-description').value.trim();
    const reminder = document.getElementById('goal-reminder').value;
    const reminderTime = document.getElementById('reminder-time')?.value;
    
    // Validate form
    if (!validateGoalForm(title, targetValue, startDate, endDate)) {
        return;
    }
    
    // Collect milestones
    const milestones = [];
    document.querySelectorAll('.milestone').forEach(milestone => {
        const description = milestone.querySelector('.milestone-description').value.trim();
        const date = milestone.querySelector('.milestone-date').value;
        if (description && date) {
            milestones.push({
                description,
                date,
                completed: false
            });
        }
    });
    
    // Create goal object
    const goal = {
        id: Date.now().toString(),
        type: goalType,
        title,
        targetValue: parseFloat(targetValue),
        unit,
        startDate,
        endDate,
        currentValue: 0, // Track progress
        description,
        reminder: {
            frequency: reminder,
            time: reminderTime
        },
        milestones,
        createdAt: new Date().toISOString(),
        progress: 0,
        status: 'active',
        motivationalQuote: document.getElementById('motivational-quote')?.textContent.trim()
    };
    
    // Save to localStorage
    saveGoalToStorage(goal);
    
    // Schedule notifications
    if (reminder !== 'none') {
        scheduleGoalReminders(goal);
    }
    
    // Show success message
    showAlert('Goal saved successfully! You can track your progress on the goals page.', 'success');
    
    // Redirect to goals list after a delay
    setTimeout(() => {
        window.location.href = 'health-goals.html';
    }, 2000);
}

/**
 * Schedule notifications for the goal
 */
function scheduleGoalReminders(goal) {
    // In a real app, this would use the Notification API or a backend service
    console.log(`Scheduled ${goal.reminder.frequency} reminders for goal "${goal.title}"`);
    
    // For demo purposes, we'll just store the reminder info
    const reminders = JSON.parse(localStorage.getItem('goalReminders')) || [];
    reminders.push({
        goalId: goal.id,
        title: goal.title,
        frequency: goal.reminder.frequency,
        time: goal.reminder.time,
        nextReminder: calculateNextReminderDate(goal.reminder.frequency, goal.reminder.time)
    });
    localStorage.setItem('goalReminders', JSON.stringify(reminders));
}

/**
 * Calculate next reminder date
 */
function calculateNextReminderDate(frequency, time) {
    const now = new Date();
    const [hours, minutes] = time.split(':').map(Number);
    
    let nextDate = new Date();
    nextDate.setHours(hours, minutes, 0, 0);
    
    switch (frequency) {
        case 'daily':
            if (now > nextDate) {
                nextDate.setDate(nextDate.getDate() + 1);
            }
            break;
        case 'weekly':
            nextDate.setDate(nextDate.getDate() + 7);
            break;
        case 'monthly':
            nextDate.setMonth(nextDate.getMonth() + 1);
            break;
    }
    
    return nextDate.toISOString();
}

/**
 * Add dynamic styles for new elements
 */
function addDynamicStyles() {
    const style = document.createElement('style');
    style.textContent = `
        /* Alert/Notification Styles */
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(120%);
            transition: transform 0.3s ease;
            z-index: 1000;
            max-width: 350px;
        }
        
        .alert.show {
            transform: translateX(0);
        }
        
        .alert-success {
            border-left: 4px solid #28a745;
        }
        
        .alert-info {
            border-left: 4px solid #2a7fba;
        }
        
        .alert-warning {
            border-left: 4px solid #ffc107;
        }
        
        .alert-error {
            border-left: 4px solid #dc3545;
        }
        
        /* Form Error Styles */
        .error {
            border-color: #dc3545 !important;
            background-color: #fff5f5;
        }
        
        .field-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
        
        /* Duration Display */
        .duration-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #666;
            margin: -10px 0 15px;
        }
        
        /* Motivational Quote */
        .motivational-quote {
            background: #f0f7ff;
            border-left: 3px solid #2a7fba;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 0 6px 6px 0;
            font-style: italic;
            display: flex;
            gap: 8px;
        }
        
        .motivational-quote i {
            color: #2a7fba;
            font-size: 16px;
        }
        
        /* Milestones Styles */
        .milestones-section {
            margin-top: 25px;
        }
        
        .milestones-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }
        
        .milestone {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .milestone .form-control {
            flex: 1;
        }
        
        .milestone-description {
            min-width: 200px;
        }
        
        .milestone-date {
            min-width: 140px;
        }
        
        /* Reminder Time Styles */
        .reminder-time-group {
            margin-top: -10px;
            margin-bottom: 15px;
        }
    `;
    document.head.appendChild(style);
}

// Add dynamic styles when the page loads
addDynamicStyles();