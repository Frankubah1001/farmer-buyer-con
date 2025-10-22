<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>

<style>
    .farmer-item {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .farmer-item:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        background-color: #f1f1f1;
    }

    .farmer-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
        transition: opacity 0.3s ease;
    }

    .farmer-item:hover .farmer-img {
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .farmer-img {
            height: 150px;
        }
    }
</style>

<section id="farmers" class="section">
    <div class="container section-title" data-aos="fade-up">
        <h2>Search Results for Farmers</h2>
        <p>Results based on your search query.</p>
    </div>
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div id="farmers-grid" class="row gy-4">
            <?php
            if (isset($_GET['query'])) {
                $query = mysqli_real_escape_string($conn, trim($_GET['query']));
                $sql = "SELECT u.full_name, u.address, pl.produce, pl.image_path
                        FROM users u
                        JOIN produce_listings pl ON u.user_id = pl.user_id
                        WHERE pl.produce LIKE ? OR u.full_name LIKE ? OR u.address LIKE ?
                        LIMIT 10";
                $searchTerm = "%$query%";
                $stmt = mysqli_prepare($conn, $sql);
                $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="col-lg-4 col-md-6">';
                        echo '<div class="farmer-item">';
                        echo '<img src="' . ($row['image_path'] ?: 'assets/img/no-image.jpg') . '" alt="' . $row['full_name'] . '" class="farmer-img">';
                        echo '<h4>' . $row['full_name'] . '</h4>';
                        echo '<p>Location: ' . $row['address'] . '</p>';
                        echo '<p>Produce: ' . $row['produce'] . '</p>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="text-center">No farmers found matching your search.</p>';
                }
                $stmt->close();
            } else {
                echo '<p class="text-center">Please enter a search query.</p>';
            }
            ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<?php mysqli_close($conn); ?>