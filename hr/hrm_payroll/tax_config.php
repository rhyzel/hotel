<?php
// Statutory contribution rates (employee share) — edit these to match your local statutory rates
// Values are fractions (e.g., 0.045 = 4.5%)
$STATUTORY_RATES = [
  'sss' => 0.045,        // SSS employee share placeholder
  'philhealth' => 0.035, // PhilHealth employee share placeholder
  'pagibig' => 0.02,     // Pag-IBIG employee share placeholder
  'withholding' => 0.10  // Withholding tax placeholder
];

// Note: these are defaults. For production, consider persisting these in a settings table or admin UI.
?>
