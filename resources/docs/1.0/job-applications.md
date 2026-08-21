# Job Application Module User Guide

## Table of Contents

- [Overview](#overview)
- [Getting the Job Application Link](#creating-a-job-application)
- [Viewing Job Applications](#viewing-job-applications)
- [Automatic Screening (Optional Criteria)](#automatic-screening)
- [Exporting Applicant Names](#exporting-applicant-names)
- [Screening Board (External Reviewers)](#screening-board)
- [Notifications](#notifications)

---

<a name="overview"></a>
## Overview

The Job Application Module allows applicants to apply for open positions via a public link, and lets HR review, score, shortlist, reject, and export applications. HR can also delegate shortlisting to an external panel through the [Screening Board](#screening-board), without giving them an HRMIS account.

---

<a name="creating-a-job-application"></a>
## Getting the Job Application Link

1. Navigate to the **Job Applications** section in the sidebar.
2. Click the **New Application** or **Apply** button for the relevant job posting.
3. Copy the link and share it with applicants, or include it in your job advert, so applicants can fill in and submit their own application.

---

<a name="viewing-job-applications"></a>
## Viewing Job Applications

1. In the sidebar, go to the **General** section.
2. Click on **Applications**.
3. A paginated table of all job applications will appear, with the most recent applications at the top.
4. Use the available filters to narrow down applications by job, time, or a combination of filter options.
5. Click the **eye icon** on a row to view the full application, change its status, or shortlist/reject it.

---

<a name="automatic-screening"></a>
## Automatic Screening (Optional Criteria)

If a job posting has screening criteria configured (minimum qualification, minimum experience, age range, and/or required keywords), every new application is automatically checked against them as soon as it is submitted:

- Applications that **fail** a hard filter (e.g. below the minimum qualification, or missing a required keyword) are automatically marked **Rejected**, with the reason recorded on the application.
- Applications that **pass** are given a weighted **score out of 100**, based on qualification, experience, keyword match, and age fit. Passing applications can be sorted by score on the applications table and on the Screening Board.

Jobs without any criteria configured skip this step — all applications are left for manual review.

---

<a name="exporting-applicant-names"></a>
## Exporting Applicant Names

HR can export a CSV of applicant names paired with the job they applied for:

1. Navigate to the **Job Applications** section.
2. Click the **Export** button.
3. A CSV file downloads listing each applicant's name alongside the job title they applied for.

---

<a name="screening-board"></a>
## Screening Board (External Reviewers)

The Screening Board lets HR share a job's applicants with an external review panel (e.g. board members without an HRMIS login) through a single link protected by a shared PIN.

**Generating a link (HR):**

1. Open the job posting and click **Generate Screening Link** (or **Regenerate** if one already exists).
2. Set a PIN for the board to use.
3. Copy the generated link and share it with the panel, along with the PIN through a separate channel.

Regenerating the link creates a new token and PIN, which immediately invalidates the old link and signs out anyone still using it.

**Using the link (Board members):**

1. Open the shared link. First-time visitors (per browser session) are prompted for the PIN.
2. Enter the PIN to unlock the applicant list for that job only.
3. Click into an applicant to view their full application.
4. Mark the applicant **Shortlisted** or **Rejected**. A reason is required when rejecting.

Board members can only view and act on applicants for the specific job tied to their link — they cannot see other jobs, and cannot move an application beyond shortlisted/rejected (later stages such as interview, offer, and hire remain HR-only). PIN attempts are rate-limited to prevent guessing.

---

<a name="notifications"></a>
## Notifications

- Applicants receive a confirmation email when their application is submitted.
- Applicants are notified by email when they are **shortlisted** or move to a later stage (interview, offer, hire).
- **Rejection emails are disabled.** Applicants are not emailed when their application is rejected, whether the rejection happens automatically (screening criteria), from the main applications table, or from the Screening Board.
