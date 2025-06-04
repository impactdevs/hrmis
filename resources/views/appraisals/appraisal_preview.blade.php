<div class="container-lg">
    <div class="container-lg preview-section">
  
    </div>


    @if ($appraisal->is_appraisee || $appraisal->is_appraisor)
        <div class="gap-3 d-flex no-print">
            <button type="submit" class="btn btn-lg btn-primary" id="proceed-btn">
                <i class="fas fa-paper-plane me-2"></i>Proceed
            </button>
        </div>
    @endif
</div>


