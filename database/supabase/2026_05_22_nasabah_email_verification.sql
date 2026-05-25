alter table public.nasabah
    add column if not exists email_verified_at timestamptz null,
    add column if not exists email_verification_token_hash text null,
    add column if not exists email_verification_expires_at timestamptz null,
    add column if not exists email_verification_sent_at timestamptz null;

-- Keep existing nasabah accounts working after the verification columns are added.
update public.nasabah
set email_verified_at = now()
where email_verified_at is null
  and email_verification_token_hash is null
  and email_verification_expires_at is null;
