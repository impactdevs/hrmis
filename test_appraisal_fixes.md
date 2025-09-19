# Appraisal Withdraw and Resubmission Fix Verification

## Overview
Fixed withdraw and resubmission functionality to properly work with `current_stage` field.

## Changes Made

### 1. Fixed `getCanBeWithdrawnAttribute` Method
- Now properly checks if appraisee is the authenticated user
- Verifies the appraisal is submitted
- Determines correct initial stage based on appraisee's role (Staff vs HOD)
- Only allows withdrawal if still at initial stage AND no approver has acted

**Key Logic:**
- For Staff appraisees: Initial stage is "Staff"
- For HOD appraisees: Initial stage is "Head of Division"
- Can withdraw only if `current_stage` matches initial stage and no approval/rejection exists

### 2. Updated `withdraw` Method
- Added validation using `can_be_withdrawn` attribute
- Properly resets appraisal status and stage
- Marks draft as not submitted
- Logs withdrawal action with proper parameters

### 3. Fixed Resubmission Logic in Controller
- Corrected stage determination for resubmissions
- For HOD appraisees: Goes directly to "Executive Secretary"
- For Staff appraisees: Goes to appropriate stage based on appraiser role:
  - If appraiser is HR: Goes to "HR"
  - If appraiser is HOD: Goes to "Head of Division"
  - Default fallback: "Head of Division"

### 4. Enhanced Error Handling
- Controller withdraw method now properly handles exceptions
- Returns user-friendly error messages

## Test Scenarios

### Scenario 1: Staff Withdrawal
1. Staff creates appraisal (current_stage: "Staff")
2. Staff submits appraisal (current_stage: "Head of Division")
3. **Before HOD acts**: Staff can withdraw ✅
4. **After HOD approves/rejects**: Staff cannot withdraw ✅

### Scenario 2: HOD Withdrawal
1. HOD creates appraisal (current_stage: "Staff")
2. HOD submits appraisal (current_stage: "Executive Secretary")
3. **Before ES acts**: HOD can withdraw ✅
4. **After ES approves/rejects**: HOD cannot withdraw ✅

### Scenario 3: Staff Resubmission After Rejection
1. Staff appraisal rejected by HOD
2. Staff edits and resubmits
3. current_stage properly set to "Head of Division" ✅

### Scenario 4: HOD Resubmission After Rejection
1. HOD appraisal rejected by ES
2. HOD edits and resubmits
3. current_stage properly set to "Executive Secretary" ✅

## Expected Behavior

### Withdrawal Rules:
- Only appraisee can withdraw their own appraisal
- Can only withdraw if submitted but not yet processed by any approver
- Must be at initial stage for their role
- Successful withdrawal resets to draft state

### Resubmission Rules:
- Only possible after rejection
- Clears all previous statuses
- Sets current_stage based on appraisee role and workflow
- Notifies appropriate next approver

## Files Modified:
1. `app/Models/Appraisal.php` - Updated withdraw functionality and attributes
2. `app/Http/Controllers/AppraisalsController.php` - Fixed resubmission logic and error handling
3. `database/migrations/2025_09_12_072031_add_current_stage_to_appraisals_table.php` - Fixed enum values

## Migration Status:
- Updated `current_stage` column enum values from `['Staff', 'HOD', 'HR', 'Executive Secretary', 'Completed']` to `['Staff', 'Head of Division', 'HR', 'Executive Secretary', 'Completed']`
- Migration refreshed to ensure database schema matches code expectations

## Issues Fixed:
1. **Parameter order error** in `AppraisalHistory::logAction()` - Fixed parameter sequence
2. **Enum value mismatch** - Migration used 'HOD' but code used 'Head of Division'
3. **Stage capture timing** - Fixed to capture original stage before reset
4. **Error handling** - Added proper exception handling in controller
