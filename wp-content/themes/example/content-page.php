<?php
/**
 * The template used for displaying page content
 *
 * @package    WordPress
 * @subpackage Twenty_Fifteen
 * @since      Twenty Fifteen 1.0
 */
$results            = false;
$country            = $_GET['country'];
$city               = $_GET['city'];
$country_population = $_GET['country_population'];
$city_population    = $_GET['city_population'];

$calc_page_id = RESULT_PAGE_ID;


if ( ! empty( $_GET ) ) {
	$results = getResults( $country, $country_population, $city, $city_population );
}

?>
<body xmlns="http://www.w3.org/1999/html">

<header class="main_header">
	<?php get_template_part( 'template-parts/navigation/navigation', 'top' ); ?>
</header>

<?php if ( get_the_ID() != $calc_page_id ) : ?>
	<?php if ( ! is_front_page() ): ?>
        <!-- .entry-header -->
		<?php the_content(); ?>
        <!-- .entry-content -->
	<?php else: ?>
        <div class="homepage_screen">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <form class="form-inline center-block clearfix search-form" role="form"
                              action="<?php echo bloginfo( 'url' ) . "?page_id={$calc_page_id}" ?>" method="GET">
                            <input hidden type="text" name="page_id" value="12">
                            <div class="form-group col-md-6">
                                <label for="country">Country</label>
                                <br>
                                <input type="text" class="form-control" id="country" name="country"
                                       value="<?php echo $country ? $country : null ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="population">Country Population</label>
                                <br>
                                <select id="country_population" class="form-control" name="country_population">
                                    <option value="" <?php echo ! $country_population ? 'selected' : null ?>></option>
                                    <option value="500K" <?php echo $country_population == '500K' ? 'selected' : null ?>>
                                        < 500 тыс
                                    </option>
                                    <option value="500K-5M" <?php echo $country_population == '500K-5M' ? 'selected' : null ?>>
                                        500 тыс. - 5М
                                    </option>
                                    <option value="5M-45M" <?php echo $country_population == '5M-45M' ? 'selected' : null ?>>
                                        5М - 45М
                                    </option>
                                    <option value="over-45M" <?php echo $country_population == 'over-45M' ? 'selected' : null ?>>
                                        > 45М
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-md-12 text-center" style="margin-top: 20px">
                                <button type="submit" class="btn btn-default ">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

	<?php endif; ?>
<?php else: ?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form class="form-inline center-block clearfix search-form" role="form"
                      action="<?php echo bloginfo( 'url' ) . "?page_id={$calc_page_id}" ?>" method="GET">
                    <input hidden type="text" name="page_id" value="12">
                    <div class="form-group col-md-3">
                        <label for="country">Country</label>
                        <br>
                        <input type="text" class="form-control" id="country" name="country"
                               value="<?php echo $country ? $country : null ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="city">City</label>
                        <br>
                        <input type="text" class="form-control" id="city" name="city"
                               value="<?php echo $city ? $city : null ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="country_population">Country Population</label>
                        <br>
                        <select id="country_population" class="form-control" name="country_population">
                            <option value="" <?php echo ! $country_population ? 'selected' : null ?>></option>
                            <option value="500K" <?php echo $country_population == '500K' ? 'selected' : null ?>>< 500
                                тыс
                            </option>
                            <option value="500K-5M" <?php echo $country_population == '500K-5M' ? 'selected' : null ?>>
                                500 тыс. - 5М
                            </option>
                            <option value="5M-45M" <?php echo $country_population == '5M-45M' ? 'selected' : null ?>>5М
                                - 45М
                            </option>
                            <option value="over-45M" <?php echo $country_population == 'over-45M' ? 'selected' : null ?>>
                                > 45М
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="city_population">City Population</label>
                        <br>
                        <select id="city_population" class="form-control" name="city_population">
                            <option value="" <?php echo ! $city_population ? 'selected' : null ?>></option>
                            <option value="50K" <?php echo $city_population == '50K' ? 'selected' : null ?>>< 50
                                тыс
                            </option>
                            <option value="50K-500K" <?php echo $city_population == '50K-500K' ? 'selected' : null ?>>50K - 500K
                            </option>
                            <option value="500K-1M" <?php echo $city_population == '500K-1M' ? 'selected' : null ?>>500K -
                                1М
                            </option>
                            <option value="over-1M" <?php echo $city_population == 'over-1M' ? 'selected' : null ?>>>
                                1М
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-md-12 text-center" style="margin-top: 20px">
                        <button type="submit" class="btn btn-default ">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="results-block">
			<?php if ( $results ): ?>
                <table id="results-table" class="display table" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Country</th>
                        <th>City</th>
                        <th>Capital</th>
                        <th>Country Population</th>
                        <th>City Population</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ( $results as $city_data ): ?>
                        <tr>
                            <td><?php echo $city_data['country_name'] ?></td>
                            <td><?php echo ucfirst($city_data['city_name']) ?></td>
                            <td><?php echo $city_data['capital'] ?></td>
                            <td><?php echo number_format($city_data['country_pop']) ?></td>
                            <td><?php echo number_format($city_data['city_pop']) ?></td>
                        </tr>
					<?php endforeach; ?>
                    </tbody>

                </table>
			<?php else: ?>
            <p>The search has not given any results</p>
			<?php endif; ?>
        </div>
    </div>

<?php endif; ?>

<script>
    var baseUrl = '<?php bloginfo( 'url' )?>';
</script>

