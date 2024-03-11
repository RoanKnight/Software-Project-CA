import torch

def check_gpu():
    if torch.cuda.is_available():
        if torch.cuda.get_device_capability()[0] >= 7:  # Check for GPU with ROCm support
            device = torch.device("cuda")
            print("ROCm compatible GPU available")
        else:
            device = torch.device("cpu")
            print("GPU available but not ROCm compatible, using CPU")
    else:
        device = torch.device("cpu")
        print("GPU not available, using CPU")
    return device

# Call the function to check GPU
device = check_gpu()

# Print the device (optional)
print(f"Using device: {device}")
