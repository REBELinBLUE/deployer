if [ ! -f {{ shared_dir }}/{{ migration }} ]; then
    echo '{{ release }}' > {{ shared_dir }}/{{ migration }}
fi
