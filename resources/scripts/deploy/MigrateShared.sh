### Migrate shared directories/files - {{ deployment }}
SHARED_NEEDS_MIGRATING=false
if [ ! -f {{ shared_dir }}/{{ migration }} ]; then
    echo ""
    echo "Shared directory needs migrating - Backup created"

    if [ ! -d {{ backup_dir }} ]; then
        mv -f {{ shared_dir }} {{ backup_dir }}
    fi

    SHARED_NEEDS_MIGRATING=true
fi
