# encrypt_file.py

A two-pass RSA file encryption tool that signs with the sender's private key and then encrypts the result with the recipient's public key.

## How It Works

```
plaintext  →  [sender's private key]  →  pass1  →  [recipient's public key]  →  .wr file
```

1. Read the input text file (truncated to 180 characters with a warning if larger)
2. Encrypt the text with the **sender's RSA private key** (PKCS1v15)
3. Encrypt the resulting blob with the **recipient's RSA public key** (PKCS1v15)
4. Save the final encrypted output next to the original file with a `.wr` extension

The `.wr` file contains only the final doubly-encrypted blob. The recipient decrypts in reverse: first with their private key, then verifies with the sender's public key to recover the plaintext.

## Requirements

```bash
pip install cryptography
```

## Usage

```bash
python encrypt_file.py <input_file> <private_key.pem> <recipient_public_key.pem>
```

### Example

```bash
python encrypt_file.py secret.txt my_private_key.pem recipient_public_key.pem
# → produces: secret.txt.wr
```

## Generating Test RSA Key Pairs

```bash
# Sender key pair
openssl genrsa -out my_private_key.pem 2048
openssl rsa -in my_private_key.pem -pubout -out my_public_key.pem

# Recipient key pair (must be larger than sender key — see note below)
openssl genrsa -out recipient_private_key.pem 4096
openssl rsa -in recipient_private_key.pem -pubout -out recipient_public_key.pem
```

## Key Size Requirement

The output of pass 1 (private key operation) is always `key_size / 8` bytes — for example, 256 bytes for a 2048-bit sender key. PKCS1v15 encryption in pass 2 can only accept up to `key_size / 8 - 11` bytes, so **the recipient's key must be larger than the sender's key** for the two-pass chain to work.

| Sender key | Minimum recipient key |
|---|---|
| 1024-bit | 2048-bit |
| 2048-bit | 4096-bit |

If the key sizes are incompatible, the program will exit with a clear error message indicating the minimum recipient key size required.

## File Size Limit

Input files are limited to **180 characters**. If the file exceeds this limit, the content is automatically truncated and a warning is printed:

```
WARNING: File contains 320 characters, which exceeds the 180-character limit. Truncating to 180 characters.
```

## Output

The encrypted file is saved in the same directory as the input file with `.wr` appended to the original filename:

| Input | Output |
|---|---|
| `secret.txt` | `secret.txt.wr` |
| `message.log` | `message.log.wr` |


