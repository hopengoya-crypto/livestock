USE livestock_vaccination_system;

ALTER TABLE `user`
    ADD COLUMN IF NOT EXISTS national_id VARCHAR(30) NULL AFTER phone_no;

ALTER TABLE livestock
    ADD COLUMN IF NOT EXISTS gender VARCHAR(10) NULL AFTER livestock_species;

CREATE TABLE IF NOT EXISTS vaccine (
    vaccine_id INT AUTO_INCREMENT PRIMARY KEY,
    vaccine_name VARCHAR(100) NOT NULL UNIQUE,
    disease_prevented VARCHAR(100) NOT NULL,
    target_species VARCHAR(20) NOT NULL,
    recommended_dosage VARCHAR(50),
    manufacturer VARCHAR(100),
    storage_requirements VARCHAR(150),
    notes TEXT
) ENGINE=InnoDB;

ALTER TABLE schedule
    ADD COLUMN IF NOT EXISTS livestock_id INT NULL AFTER farmer_id,
    ADD COLUMN IF NOT EXISTS vaccine_id INT NULL AFTER vet_id,
    ADD COLUMN IF NOT EXISTS scheduled_time TIME NULL,
    ADD COLUMN IF NOT EXISTS notes TEXT NULL;

ALTER TABLE schedule
    ADD CONSTRAINT fk_schedule_livestock
        FOREIGN KEY (livestock_id) REFERENCES livestock (livestock_id),
    ADD CONSTRAINT fk_schedule_vaccine
        FOREIGN KEY (vaccine_id) REFERENCES vaccine (vaccine_id);

ALTER TABLE `record`
    ADD COLUMN IF NOT EXISTS next_vaccination_date DATE NULL,
    ADD COLUMN IF NOT EXISTS vaccination_status VARCHAR(30) NOT NULL DEFAULT 'completed',
    ADD COLUMN IF NOT EXISTS remarks TEXT NULL;

CREATE TABLE IF NOT EXISTS veterinarian_request (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT NOT NULL,
    vet_id INT NOT NULL,
    request_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vet_request_farmer
        FOREIGN KEY (farmer_id) REFERENCES farmer (farmer_id),
    CONSTRAINT fk_vet_request_veterinarian
        FOREIGN KEY (vet_id) REFERENCES veterinarian (vet_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS veterinarian_invitation (
    invitation_id INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT NOT NULL,
    email_address VARCHAR(50) NOT NULL,
    invitation_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    invited_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vet_invitation_farmer
        FOREIGN KEY (farmer_id) REFERENCES farmer (farmer_id)
) ENGINE=InnoDB;

ALTER TABLE system_reports
    ADD COLUMN IF NOT EXISTS start_date DATE NULL,
    ADD COLUMN IF NOT EXISTS end_date DATE NULL,
    ADD COLUMN IF NOT EXISTS report_status VARCHAR(30) NOT NULL DEFAULT 'requested',
    ADD COLUMN IF NOT EXISTS generated_by INT NULL AFTER report_status;

ALTER TABLE system_reports
    ADD CONSTRAINT fk_report_generated_by
        FOREIGN KEY (generated_by) REFERENCES `user` (user_id);
