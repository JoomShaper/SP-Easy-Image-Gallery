---
name: joomla-6-security
description: Security-focused development and code review guidelines for Joomla 6 extensions. Use when developing, reviewing, fixing, or auditing Joomla 6 PHP, MVC, AJAX, API, database, filesystem, and administrator code.
---

# Joomla 6 Security

Apply Joomla 6 security best practices when developing or reviewing extensions.

## Core Rules

### Authorization

- Always verify that the current user is authorized to perform the requested action.
- Use Joomla ACL checks.
- Never rely on hidden UI controls as authorization.
- Enforce permissions server-side for every sensitive operation.
- Use appropriate permissions such as `core.manage`, `core.create`, `core.edit`, `core.edit.own`, `core.delete`, and `core.edit.state`.
- Do not assume administrator context means the user is authorized.
- Verify resource ownership where applicable.

### Input Validation

- Treat all request data as untrusted.
- Validate input according to its expected type.
- Use Joomla's Input API.
- Prefer allowlists over blacklists.
- Never trust IDs, paths, filenames, URLs, or user-provided HTML.
- Validate data before using it in database, filesystem, redirect, or external-request operations.
- Do not rely on client-side validation.

Example:

```php
$id = $this->input->getInt('id');
```

### Output Escaping

- Escape data according to its output context.
- HTML text and attributes must be properly escaped.
- Validate URLs before outputting them.
- Use context-safe encoding for JavaScript and JSON.
- Never assume database content is trusted.
- Do not blindly escape intentionally allowed HTML; sanitize it according to the required HTML policy.

Example:

```php
echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
```

### SQL Security

- Never concatenate untrusted input into SQL queries.
- Use Joomla's database query API.
- Use bound parameters for values.
- Use `quoteName()` for database identifiers.
- Validate numeric IDs with `getInt()`.
- Never construct SQL using raw request data.

Example:

```php
$query = $db->getQuery(true)
    ->select($db->quoteName(['id', 'title']))
    ->from($db->quoteName('#__example'))
    ->where($db->quoteName('id') . ' = :id')
    ->bind(':id', $id);

$db->setQuery($query);
$row = $db->loadObject();
```

### CSRF Protection

- Protect all state-changing requests against CSRF.
- Use Joomla's session token mechanism.
- Verify tokens server-side.
- Do not consider POST alone sufficient CSRF protection.
- Apply CSRF protection to AJAX actions where applicable.

### AJAX Endpoints

Treat every AJAX endpoint as a security boundary.

For each endpoint:

1. Validate the request.
2. Verify CSRF protection where applicable.
3. Verify authentication when required.
4. Verify ACL permissions.
5. Verify resource ownership where applicable.
6. Perform the requested operation.
7. Return only authorized data.

Never assume an AJAX endpoint is protected because it is called only from JavaScript.

### Administrator Actions

- Administrator URLs are not automatically secure.
- Verify authentication.
- Verify ACL permissions.
- Validate all request parameters.
- Verify ownership or resource-level authorization.
- Protect state-changing actions against CSRF.
- Do not rely on the administrator URL as a security boundary.

### File Operations

Treat filenames and paths as untrusted input.

Prevent:

- Path traversal
- Arbitrary file read
- Arbitrary file write
- Arbitrary file deletion
- Unsafe file uploads
- Executable file uploads

- Use Joomla filesystem APIs where appropriate.
- Validate paths against the intended directory.
- Do not construct filesystem paths directly from untrusted input.

### File Uploads

- Validate uploaded files server-side.
- Never trust the original filename.
- Never trust client-provided MIME types.
- Restrict extensions and MIME types.
- Generate safe filenames.
- Restrict upload destinations.
- Prevent executable uploads.
- Enforce file-size limits.

### Redirects

- Do not allow arbitrary user-controlled redirects.
- Validate redirect destinations.
- Prefer Joomla's URL handling mechanisms.
- Pay particular attention to parameters such as `return`, `redirect`, `url`, `link`, `next`, and `returnUrl`.

### XSS Prevention

Check for:

- Stored XSS
- Reflected XSS
- DOM-based XSS
- Administrator-side XSS
- Attribute injection
- JavaScript injection
- Unsafe HTML rendering

Important:

- Administrator users can also be victims of stored XSS.
- Never assume administrator-entered content is automatically safe.
- Escape output according to its context.

### Template and File Rendering

- Never allow arbitrary user input to select PHP files, templates, layouts, views, or include paths.
- Use strict allowlists for dynamic file selection.
- Do not allow user-controlled values to become executable PHP paths.

### Deserialization

- Avoid unsafe deserialization of untrusted data.
- Never unserialize attacker-controlled data unless the data source is strictly trusted and controlled.
- Prefer JSON or structured formats where appropriate.

### External Requests

Validate and restrict user-controlled URLs before making server-side requests.

Consider:

- SSRF
- localhost access
- private network access
- cloud metadata endpoints
- redirects to internal services
- unexpected protocols

Never allow arbitrary user input to become an unrestricted outbound server request.

### Secrets

Never expose or log:

- API keys
- Passwords
- Access tokens
- Database credentials
- Private keys
- Other sensitive credentials

Never include secrets in error messages or source code.

### Error Handling

Do not expose sensitive internal information to users.

Avoid exposing:

- SQL queries
- Filesystem paths
- Stack traces
- Credentials
- Internal service URLs
- Configuration secrets

Use Joomla logging facilities for appropriate diagnostic information.

## Security Review Method

When reviewing Joomla 6 code:

1. Identify all externally controllable inputs.
2. Trace inputs to sensitive operations.
3. Identify authentication requirements.
4. Identify authorization checks.
5. Check CSRF protection.
6. Check input validation.
7. Check output encoding.
8. Check SQL construction.
9. Check filesystem operations.
10. Check redirects and external requests.
11. Check error handling and information disclosure.
12. Check whether the same operation is reachable through alternate endpoints.
13. Verify that security controls are enforced server-side.

Do not stop after finding a client-side restriction. Verify the actual server-side security boundary.

## Security Principles

Security controls must be enforced where the sensitive operation occurs.

- Hiding a delete button is not authorization.
- Requiring POST is not CSRF protection.
- Restricting an AJAX button in JavaScript is not access control.
- Checking permissions only on the main page does not secure a separate AJAX endpoint.
- Client-side validation does not replace server-side validation.
- Administrator access does not automatically grant permission for every extension operation.

## Security Findings

When documenting a security issue, provide:

- Title
- Severity
- Affected component
- Affected endpoint or view
- Preconditions
- Required privilege level
- Attack surface
- Root cause
- Impact
- Reproduction summary
- Recommended remediation

Clearly distinguish:

- Authentication requirements
- Authorization requirements
- CSRF requirements
- Input requirements
- Resource ownership requirements

Do not claim an issue is fixed unless the actual server-side security boundary has been verified.
