# Test Implementation Plan

## 1. Overview & Guidelines
- **Framework**: Use **Pest** for all new tests (`it('does something', function () { ... })`).
- **Database**: Use `RefreshDatabase` for all Feature/Integration tests to ensure a clean state per test.
- **Mocking**: Skip assertions for the `N8nEmailService` since it is currently down. Use standard `Mail::fake()` or just omit email dispatch checks where the service is used.
- **Authentication**: Use `auth('api')->claims(['token_type' => 'access'])->fromUser($user)` to generate JWT tokens for acting as specific users.

---

## 2. Unit Tests (`tests/Unit/`)

### 2.1 Models & Relationships
- **Ad & Project**: Test that `Ad` `hasMany` `AdApplication` and `Project` `hasMany` `ProjectApplication`.
- **User & Profiles**: Test that `Student`, `Customer`, and `Company` correctly resolve their `Profile` relationships and `savedStudents`/`savedAds` pivot relationships.

---

## 3. Feature Tests (`tests/Feature/`)

### 3.1 Auth (`tests/Feature/Auth/`)
- **`AuthApiTest.php`**:
  - `POST /auth/login`: Test login failure with bad credentials. Test login success returns JWT and user payload.
  - `POST /auth/logout`: Test JWT invalidation (token version increment).
  - `POST /auth/refresh`: Test fetching a new JWT using a valid refresh token.
  - `POST /auth/send-email`: Test password reset OTP generation logic.
  - `POST /auth/change-password`: Test updating password using a valid reset token.

### 3.2 Profiles (`tests/Feature/Profiles/`)
- **`ProfileApiTest.php`**:
  - Test `GET /students`, `GET /companies`, `GET /customers` list logic (including search filtering).
  - Test `GET /students/{id}` (show).
  - Test `PUT /companies/{id}` (or profile update endpoint): Ensure a user can only update their *own* profile, and that both `users` and `profile` tables are updated successfully.

### 3.3 Saved Features (`tests/Feature/SavedItems/`)
- **`CompanySavedStudentsTest.php`**:
  - Test `POST /saved-students/{student}`: Company successfully saves a student.
  - Test `GET /saved-students`: Returns the list of saved students for the authenticated company.
  - Test `DELETE /saved-students/{student}`: Removes the student from the saved list.
- **`StudentSavedAdsTest.php`**:
  - Test `POST /student/saved-ads/{ad}`: Student saves an ad.
  - Test `GET /student/saved-ads`: Lists ads saved by the student.
  - Test `DELETE /student/saved-ads/{ad}`: Removes the ad.

### 3.4 Applications (`tests/Feature/Applications/`)
- **`AdApplicationTest.php`**:
  - `POST /ad-applications`: Student applies to an ad successfully.
  - `PUT /ad-applications/{id}`: Student updates their application (e.g., changes expected salary).
  - `DELETE /ad-applications/{id}`: Student withdraws application.
  - `GET /student/my-ad-applications`: Student can view their application history.
- **`ProjectApplicationTest.php`**:
  - Parity with the Ad Application tests, but targeting project endpoints.

---

## 4. Integration Tests (Complex Flows)

These tests validate multi-user, end-to-end interactions inside the system. They should be placed in `tests/Feature/Flows/`.

### 4.1 Company Ad Lifecycle Flow (`AdLifecycleFlowTest.php`)
1. **Act 1**: Company A authenticates and creates a new Ad (`POST /ads`).
2. **Act 2**: Student B authenticates, views the ads, and saves Company A's Ad to their favorites (`POST /student/saved-ads/{ad}`).
3. **Act 3**: Student B applies to the Ad (`POST /ad-applications`), submitting a resume and GitHub link.
4. **Act 4**: Company A calls the application list API (`GET /ads/{ad}/applications`).
5. **Assert**: The response contains Student B's application, verifying `created_at` timestamps and expected data structures.

### 4.2 Customer Project Lifecycle Flow (`ProjectLifecycleFlowTest.php`)
1. **Act 1**: Customer authenticates and creates a Project (`POST /projects`).
2. **Act 2**: Student authenticates and applies to the Project (`POST /project-applications`).
3. **Act 3**: Customer views applications on their project (`GET /projects/{project}/applications`).
4. **Assert**: Ensure the student's application is visible to the customer and structured correctly.

### 4.3 Company Scouting Flow (`ScoutingFlowTest.php`)
1. **Act 1**: Company authenticates and searches for a specific student name (`GET /students?name=John`).
2. **Act 2**: Company views the student's detailed profile (`GET /students/{id}`).
3. **Act 3**: Company saves the student for later (`POST /saved-students/{id}`).
4. **Act 4**: Company retrieves their saved students list (`GET /saved-students`).
5. **Assert**: The searched student appears accurately in the company's saved list.
