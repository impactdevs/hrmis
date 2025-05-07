-- THIS FILE IS FOR INSRERTING THE APPLICATION FORM TO THE DATABASE IF IT DOES NOT EXISTS

INSERT INTO `forms` (`uuid`, `name`, `status`, `created_at`, `updated_at`) VALUES
('5b39330c-9bed-4289-a60b-d19947d5f5d9', 'JOB APPLICATION FORM', 1, '2025-05-07 10:08:40', '2025-05-07 10:08:40');


-- iNSERT FORM SECTIONS THAT WILL CONTAIN FORM FIELDS

INSERT INTO `sections` (`id`, `form_id`, `section_name`, `section_description`, `created_at`, `updated_at`) VALUES
(2, '5b39330c-9bed-4289-a60b-d19947d5f5d9', 'INTRODUCTION', 'Tell us about yourself', NULL, NULL),
(3, '5b39330c-9bed-4289-a60b-d19947d5f5d9', 'PRESENT WORK INFORMATION', 'This section seeks to know your present work information', NULL, NULL),
(4, '5b39330c-9bed-4289-a60b-d19947d5f5d9', 'Details of Schools/Institutions attended:', 'this section is for providing your academimic background information', NULL, NULL),
(5, '5b39330c-9bed-4289-a60b-d19947d5f5d9', 'WORK BACKGROUND', 'Provide your work background information', NULL, NULL),
(6, '5b39330c-9bed-4289-a60b-d19947d5f5d9', 'CRIME HISTORY', NULL, NULL, NULL),
(7, '5b39330c-9bed-4289-a60b-d19947d5f5d9', 'APPOINTMENT AVAILABILITY WHEN SELECTED', NULL, NULL, NULL),
(9, '5b39330c-9bed-4289-a60b-d19947d5f5d9', 'REFERENCES', NULL, NULL, NULL);

-- THEN ADD FORM FIELDS
INSERT INTO `form_fields` (`id`, `label`, `type`, `options`, `repeater_options`, `section_id`, `created_at`, `updated_at`) VALUES
(1, 'Full name (Surname first in capital letters)', 'text', NULL, '[{\"field\":null,\"type\":null}]', 2, '2025-05-07 10:16:07', '2025-05-07 10:16:07'),
(2, 'Date of Birth', 'date', NULL, '[{\"field\":null,\"type\":null}]', 2, '2025-05-07 10:16:32', '2025-05-07 10:16:32'),
(3, 'Postal Address', 'textarea', NULL, '[{\"field\":null,\"type\":null}]', 2, '2025-05-07 10:16:44', '2025-05-07 10:16:44'),
(4, 'E-mail Address', 'text', NULL, '[{\"field\":null,\"type\":null}]', 2, '2025-05-07 10:17:12', '2025-05-07 10:17:12'),
(5, 'Telephone Number', 'text', NULL, '[{\"field\":null,\"type\":null}]', 2, '2025-05-07 10:17:30', '2025-05-07 10:17:30'),
(6, 'Nationality', 'text', NULL, '[{\"field\":null,\"type\":null}]', 2, '2025-05-07 10:17:47', '2025-05-07 10:17:47'),
(7, 'Home District', 'text', NULL, '[{\"field\":null,\"type\":null}]', 2, '2025-05-07 10:17:59', '2025-05-07 10:17:59'),
(8, 'Sub-county', 'text', NULL, '[{\"field\":null,\"type\":null}]', 2, '2025-05-07 10:18:17', '2025-05-07 10:18:17'),
(9, 'Village', 'text', NULL, '[{\"field\":null,\"type\":null}]', 2, '2025-05-07 10:18:30', '2025-05-07 10:18:30'),
(10, 'Are you a temporary or permanent resident in  Uganda?', 'radio', 'Temporary, Permanent', '[{\"field\":null,\"type\":null}]', 2, '2025-05-07 10:19:27', '2025-05-07 10:19:27'),
(11, 'Present Ministry/Local Government/ Department/Any other Employer', 'text', NULL, '[{\"field\":null,\"type\":null}]', 3, '2025-05-07 10:20:46', '2025-05-07 10:20:46'),
(12, 'Present Post', 'text', NULL, '[{\"field\":null,\"type\":null}]', 3, '2025-05-07 10:21:28', '2025-05-07 10:21:28'),
(13, 'Date Of appointment to the present post', 'date', NULL, '[{\"field\":null,\"type\":null}]', 3, '2025-05-07 10:21:58', '2025-05-07 10:21:58'),
(14, 'Present Salary and Scale (if applicable)', 'text', NULL, '[{\"field\":null,\"type\":null}]', 3, '2025-05-07 10:22:20', '2025-05-07 10:22:20'),
(15, 'Terms of Employment (Tick as appropriate)', 'radio', 'Temporary,Contract/Probation,Permanent', '[{\"field\":null,\"type\":null}]', 3, '2025-05-07 10:23:20', '2025-05-07 10:23:20'),
(16, 'Marital Status (Tick as appropriate)', 'radio', 'Married,Single,Widowed,Divorced,Separated', '[{\"field\":null,\"type\":null}]', 3, '2025-05-07 10:24:13', '2025-05-07 10:24:13'),
(17, 'Number and age of Children', 'number', NULL, '[{\"field\":null,\"type\":null}]', 3, '2025-05-07 10:24:32', '2025-05-07 10:24:32'),
(18, 'Details of Schools/Institutions attended:', 'repeater', NULL, '[{\"field\":\"Start Date\",\"type\":\"date\"},{\"field\":\"End Date\",\"type\":\"date\"},{\"field\":\"School\\/Institution\",\"type\":\"text\"},{\"field\":\"Award\\/Qualification\",\"type\":\"text\"}]', 4, '2025-05-07 10:27:21', '2025-05-07 10:27:21'),
(19, 'Did ypou Do UCE?', 'radio', 'Yes, No', '[{\"field\":null,\"type\":null}]', 4, '2025-05-07 10:28:19', '2025-05-07 10:28:19'),
(20, 'If Yes, Which year Did you do it?', 'date', NULL, '[{\"field\":null,\"type\":null}]', 4, '2025-05-07 10:29:03', '2025-05-07 10:29:03'),
(21, 'Indicate the subject and level of passes', 'repeater', NULL, '[{\"field\":\"Subject\",\"type\":\"text\"},{\"field\":\"Grade\",\"type\":\"text\"}]', 4, '2025-05-07 10:30:46', '2025-05-07 10:30:46'),
(22, 'Did you do UACE?', 'radio', 'Yes, No', '[{\"field\":null,\"type\":null}]', 4, '2025-05-07 10:31:41', '2025-05-07 10:31:41'),
(23, 'which year did you do UACE?', 'date', NULL, '[{\"field\":null,\"type\":null}]', 4, '2025-05-07 10:32:10', '2025-05-07 10:32:10'),
(24, 'Indicate the subject and level of passes?', 'repeater', NULL, '[{\"field\":\"Subject\",\"type\":\"text\"},{\"field\":\"Grade\",\"type\":\"text\"}]', 4, '2025-05-07 10:33:12', '2025-05-07 10:33:12'),
(26, 'employment Records', 'repeater', NULL, '[{\"field\":\"Start  Date\",\"type\":\"date\"},{\"field\":\"End Date\",\"type\":\"date\"},{\"field\":\"Designation\",\"type\":\"text\"},{\"field\":\"Employer\",\"type\":\"text\"}]', 5, '2025-05-07 10:38:27', '2025-05-07 10:38:27'),
(27, 'Have you ever been convicted on a criminal charge?', 'radio', 'Yes, No', '[{\"field\":null,\"type\":null}]', 6, '2025-05-07 10:39:23', '2025-05-07 10:39:23'),
(28, 'If so, give brief details including sentence imposed', 'textarea', NULL, '[{\"field\":null,\"type\":null}]', 6, '2025-05-07 10:39:47', '2025-05-07 10:39:47'),
(30, 'How soon would you be available for appointment if selected?', 'text', NULL, '[{\"field\":null,\"type\":null}]', 7, '2025-05-07 10:41:18', '2025-05-07 10:41:18'),
(31, 'State the minimum salary expectation', 'text', NULL, '[{\"field\":null,\"type\":null}]', 7, '2025-05-07 10:41:35', '2025-05-07 10:41:35'),
(33, 'In the case of applicants not already in Government Service, the names and addresses of two responsible persons(not relatives) to whom reference can be made as regards character and ability and should be given here', 'repeater', NULL, '[{\"field\":\"Full Name\",\"type\":\"text\"},{\"field\":\"Email\",\"type\":\"text\"},{\"field\":\"phone number\",\"type\":\"text\"}]', 9, '2025-05-07 10:45:25', '2025-05-07 10:45:25');
