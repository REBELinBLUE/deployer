# Create the releases directory if it doesn't exist
if [ ! -d {{ releases_path }} ]; then
    mkdir {{ releases_path }}
fi

# Create the shared directory if it doesn't exist
if [ ! -d {{ shared_path }} ]; then
    mkdir {{ shared_path }}
fi
