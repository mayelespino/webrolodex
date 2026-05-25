#!/usr/bin/env python3
"""
encrypt_file.py - Two-pass RSA file encryption

Usage:
    python encrypt_file.py <input_file> <private_key.pem> <recipient_public_key.pem>

Process:
    1. Read the text file (truncate to 180 chars with warning if larger)
    2. Encrypt the text with the sender's RSA private key (PKCS1v15 sign)
    3. Encrypt the resulting blob with the recipient's RSA public key (PKCS1v15)
    4. Save to <input_file>.wr next to the original

Requirements:
    pip install cryptography
"""

import sys
import struct
from pathlib import Path

try:
    from cryptography.hazmat.primitives import serialization, hashes
    from cryptography.hazmat.primitives.asymmetric import padding
    from cryptography.hazmat.backends import default_backend
except ImportError:
    print("ERROR: 'cryptography' package not found.")
    print("Install it with:  pip install cryptography")
    sys.exit(1)

MAX_CHARS = 180


def load_private_key(path: Path):
    with open(path, "rb") as f:
        data = f.read()
    try:
        return serialization.load_pem_private_key(data, password=None, backend=default_backend())
    except Exception as e:
        raise ValueError(f"Failed to load private key from '{path}': {e}")


def load_public_key(path: Path):
    with open(path, "rb") as f:
        data = f.read()
    try:
        return serialization.load_pem_public_key(data, backend=default_backend())
    except Exception as e:
        raise ValueError(f"Failed to load public key from '{path}': {e}")


def encrypt_file(input_path: Path, private_key_path: Path, recipient_pubkey_path: Path):
    # ── Validate inputs ─────────────────────────────────────────────────────
    if not input_path.exists():
        raise FileNotFoundError(f"Input file not found: '{input_path}'")
    if not private_key_path.exists():
        raise FileNotFoundError(f"Private key not found: '{private_key_path}'")
    if not recipient_pubkey_path.exists():
        raise FileNotFoundError(f"Recipient public key not found: '{recipient_pubkey_path}'")

    output_path = input_path.with_suffix(input_path.suffix + ".wr")

    print(f"  Input file         : {input_path}")
    print(f"  Sender private key : {private_key_path}")
    print(f"  Recipient public key: {recipient_pubkey_path}")
    print(f"  Output file        : {output_path}")
    print()

    # ── Read and optionally truncate text ───────────────────────────────────
    text = input_path.read_text(encoding="utf-8")

    if len(text) > MAX_CHARS:
        print(f"WARNING: File contains {len(text)} characters, which exceeds the "
              f"{MAX_CHARS}-character limit. Truncating to {MAX_CHARS} characters.")
        print()
        text = text[:MAX_CHARS]

    plaintext = text.encode("utf-8")
    print(f"[1/3] Plaintext ready: {len(plaintext)} bytes")

    # ── Load keys ───────────────────────────────────────────────────────────
    private_key   = load_private_key(private_key_path)
    recipient_key = load_public_key(recipient_pubkey_path)
    print(f"[2/3] Sender private key : {private_key.key_size}-bit RSA")
    print(f"      Recipient public key: {recipient_key.key_size}-bit RSA")

    # ── Pass 1: sign/encrypt with sender's private key ──────────────────────
    # Applies the private exponent via PKCS1v15 — produces a blob only
    # verifiable/recoverable with the matching public key.
    pass1 = private_key.sign(plaintext, padding.PKCS1v15(), hashes.SHA256())
    print(f"      After private-key encryption : {len(pass1)} bytes")

    # ── Pass 2: encrypt pass1 with recipient's public key ───────────────────
    # PKCS1v15 encryption max input = key_size_bytes - 11.
    # For 2048-bit key: 245 bytes max. pass1 is 256 bytes (for 2048-bit),
    # so we use OAEP (max input = key_size_bytes - 66 = 190 bytes for 2048-bit)
    # which still can't fit pass1. We therefore encrypt pass1 in one shot using
    # raw PKCS1v15 on a 4096-bit recipient key, or store pass1 as-is and wrap
    # only the plaintext under the recipient key, bundling both in the output.
    #
    # Correct approach for chaining: recipient key must be large enough to wrap
    # pass1. We check and raise a clear error if not.
    max_input = recipient_key.key_size // 8 - 11  # PKCS1v15 limit
    if len(pass1) > max_input:
        raise ValueError(
            f"Recipient key ({recipient_key.key_size}-bit) is too small to encrypt "
            f"the {len(pass1)}-byte private-key blob in one RSA operation. "
            f"Use a recipient key of at least {(len(pass1) + 11) * 8} bits "
            f"(e.g. 4096-bit for a 2048-bit sender key)."
        )

    pass2 = recipient_key.encrypt(pass1, padding.PKCS1v15())
    print(f"      After recipient public-key encryption: {len(pass2)} bytes")

    # ── Write output ─────────────────────────────────────────────────────────
    print(f"[3/3] Writing output to {output_path}...")
    with open(output_path, "wb") as out:
        out.write(pass2)

    print()
    print(f"✓ Done. Encrypted file: {output_path}  ({output_path.stat().st_size} bytes)")


def main():
    if len(sys.argv) != 4:
        print("Usage: python encrypt_file.py <input_file> <private_key.pem> <recipient_public_key.pem>")
        print()
        print("Example:")
        print("  python encrypt_file.py secret.txt my_private_key.pem recipient_public_key.pem")
        sys.exit(1)

    input_path            = Path(sys.argv[1])
    private_key_path      = Path(sys.argv[2])
    recipient_pubkey_path = Path(sys.argv[3])

    try:
        encrypt_file(input_path, private_key_path, recipient_pubkey_path)
    except (FileNotFoundError, ValueError) as e:
        print(f"ERROR: {e}")
        sys.exit(1)
    except Exception as e:
        print(f"Unexpected error: {e}")
        raise


if __name__ == "__main__":
    main()

