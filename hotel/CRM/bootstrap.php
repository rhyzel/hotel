<?php
// Simple bootstrap for CRM module
require_once __DIR__ . '/lib/Guest.php';
require_once __DIR__ . '/lib/GuestRepository.php';
require_once __DIR__ . '/lib/GuestService.php';
require_once __DIR__ . '/lib/LoyaltyProgram.php';
require_once __DIR__ . '/lib/LoyaltyProgramRepository.php';
require_once __DIR__ . '/lib/LoyaltyProgramService.php';
// New OOP additions
require_once __DIR__ . '/lib/ApiDatabase.php';
require_once __DIR__ . '/lib/GuestController.php';
require_once __DIR__ . '/lib/ComplaintController.php';
require_once __DIR__ . '/lib/FeedbackRepository.php';
require_once __DIR__ . '/lib/FeedbackController.php';
require_once __DIR__ . '/lib/Campaign.php';
require_once __DIR__ . '/lib/CampaignService.php';
require_once __DIR__ . '/lib/CampaignRepository.php';

// You can expand this to a PSR-4 autoloader or composer in the future.
