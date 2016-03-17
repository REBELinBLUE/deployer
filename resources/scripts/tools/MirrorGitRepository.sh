chmod +x "{$wrapper}" && \
export GIT_SSH="{$wrapper}" && \
( [ ! -d {$mirror_dir} ] && git clone --mirror %s {$mirror_dir} || cd . ) && \
cd {$mirror_dir} && \
git fetch --all --prune
