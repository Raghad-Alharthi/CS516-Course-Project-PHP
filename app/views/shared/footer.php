</div> <!-- /.container -->
</body>
</html>

<script>
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            const className = button.getAttribute('data-class-name');
            const formId = button.getAttribute('data-form-id');

            Swal.fire({
                title: 'Are you sure?',
                text: `Are you sure you want to delete ${className}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
            
        });
    });
    </script>
