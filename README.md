## A place to store your public keys

You can store your public key in to the `./public_keys` directory. **Make sure you store only your PUBLIC key.** 

Save it in a file with this convention: `name.name.pub` for example `mayel.espino.pub`.

Go to the [webrolodex.com](https://webrolodex.com) page to search for a public key.


## Encrypt Tool

To encrypt and decrypt messages, use the script in the [`encrypt_file`](https://github.com/mayelespino/webrolodex/tree/main/encrypt_file) directory.

It handles the full double-encryption flow described above — encrypting with the sender's private key and the recipient's public key — and can be run directly from the command line.

Clone or download the file, then follow the instructions in its comments to set up your key paths.

