<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - DepEd BAC Tracking System</title>
    <link rel="stylesheet" href="assets/css/Index.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="assets/css/background.css">

    
</head>
<body>
    <?php
    // $showTitleRight is already set to false above
    include 'header.php';
    ?>

    <div class="main-content-wrapper">
        <div class="table-top-bar">
            <div class="left-controls">
                <button class="add-pr-button" id="showAddProjectForm">
                    <img src="assets/images/Add_Button.png" alt="Add" class="add-pr-icon">
                    Add Project
                </button>
            </div>

            <div class="center-search">
                <input type="text" id="searchInput" class="dashboard-search-bar" placeholder="Search by PR Number or Project Details..." value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <div class="right-controls">
                <button class="view-stats-button" onclick="loadAndShowStatistics()">
                    <img src="assets/images/stats_icon.png" alt="Stats" style="width:24px;height:24px;">
                    View Statistics
                </button>
            </div>
        </div>

        <?php if (!empty($deleteProjectError)): // Display delete error on main page ?>
            <p style="color: red; text-align: center; margin-top: 10px;"><?php echo htmlspecialchars($deleteProjectError); ?></p>
        <?php endif; ?>
        

        <div class="container" style="padding: 3vh 2.5vw;">
            <table class="w3-table-all w3-hoverable dashboard-table">
                <thead>
                    <tr class="w3-red">
                        <th style="width:minimum-content;"> MODE OF PROCUREMENT</th>
                        <th style="width:minimum-content;">PR NUMBER</th>
                        <th style="width:500px;">PROJECT DETAILS</th>
                        <th style="width:minimum-content;">PROJECT OWNER</th> 
                        <th style="width:minimum-content;">CREATED BY</th>
                        <th style="width:150px;">DATE CREATED</th>
                        <th style="width:150px;">DATE EDITED</th>
                        <th style="width:minimum-content;">STATUS</th>
                        <th style="width:120px;">ACTIONS</th> 
                    </tr>
                </thead>
                <tbody>

                </tbody>
                    <?php if (count($projects) > 0): ?>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                 <td>
                                    <?php
                                    if (!empty($project['MoPID'])) {
                                        // You may want to join mode_of_procurement in your SQL for display
                                        echo htmlspecialchars($mopList[$project['MoPID']] ?? 'N/A');
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td data-label="PR Number" class="pr-number-cell">
                                    <?php echo htmlspecialchars($project['prNumber']); ?>
                                </td>
                                <td data-label="Project Details">
                                    <?php
                                        $details = htmlspecialchars($project['projectDetails']);
                                        $maxLength = 80; // Adjust as needed (character count)
                                        $id = 'details_' . $project['projectID'];
                                        if (mb_strlen($details) > $maxLength) {
                                            $short = mb_substr($details, 0, $maxLength) . '...';
                                            echo '<span class="project-details-short" id="' . $id . '_short">' . $short . ' <button class="see-more-btn" onclick="showFullDetails(\'' . $id . '\')">See more</button></span>';
                                            echo '<span class="project-details-full" id="' . $id . '_full" style="display:none;">' . $details . ' <button class="see-less-btn" onclick="hideFullDetails(\'' . $id . '\')">See less</button></span>';
                                        } else {
                                            echo $details;
                                        }
                                    ?>
                                </td>
                                <td data-label="Project Owner">
                                    <?php
                                        echo htmlspecialchars($project['programOwner'] ?? 'N/A');
                                        if (!empty($project['programOffice'])) {
                                            echo " <br>(" . htmlspecialchars($project['programOffice']) . ")";
                                        }
                                    ?>
                                </td>
                                <td data-label="Created By">
                                    <?php
                                        if (!empty($project['firstname']) && !empty($project['lastname'])) {
                                            echo htmlspecialchars(substr($project['firstname'], 0, 1) . ". " . $project['lastname']);
                                        } else {
                                            echo "N/A";
                                        }
                                    ?>
                                </td>
                                <td data-label="Date Created"><?php echo date("m-d-Y", strtotime($project['createdAt'])); ?></td>
                                <td data-label="Date Edited"><?php echo date("m-d-Y", strtotime($project['editedAt'])); ?></td>
                                <td data-label="Status">
                                    <?php
                                        if ($project['notice_to_proceed_submitted'] == 1) {
                                            echo 'Finished';
                                        } else {
                                            echo htmlspecialchars($project['first_unsubmitted_stage'] ?? 'No Stages Started');
                                        }
                                    ?>
                                </td>
                                <td data-label="Actions">
                                    <a href="<?php echo url('edit_project.php', ['projectID' => $project['projectID']]); ?>" class="edit-project-btn" title="Edit Project" style="margin-right: 5px;">
                                        <img src="assets/images/Edit_Icon.png" alt="Edit Project" style="width:24px;height:24px;">
                                    </a>
                                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                                    <a href="<?php echo url('index.php', ['deleteProject' => $project['projectID']]); ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this project and all its stages?');" title="Delete Project">
                                        <img src="assets/images/delete.png" alt="Delete Project" style="width:24px;height:24px;">
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" id="noResults" style="display: block;">No projects found.</td> </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="pagination-controls">
                <div class="pagination-arrows">
                    <button class="pagination-arrow" id="prevPage">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <button class="pagination-arrow" id="nextPage">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                <div class="lines-per-page">
                    <span>LINES PER PAGE</span>
                    <select id="linesPerPage">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div id="addProjectModal" class="modal">
        <div class="modal-content">
            <span class="close" id="addProjectClose">&times;</span>
            <h2>Add Project</h2>
            <?php if (!empty($projectError)): // Display add project error inside the modal ?>
                <p style="color: red; text-align: center; margin-bottom: 10px;"><?php echo htmlspecialchars($projectError); ?></p>
            <?php endif; ?>
            <form id="addProjectForm" action="<?php echo url('index.php'); ?>" method="post">
                <label for="MoPID">Mode of Procurement*</label>
                <select name="MoPID" id="MoPID" required>
                    <option value="" disabled selected>Select Mode of Procurement</option>
                    <?php foreach ($mopList as $id => $desc): ?>
                        <option value="<?php echo $id; ?>" <?php echo (isset($_POST['MoPID']) && $_POST['MoPID'] == $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($desc); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="prNumber">Project Number (PR Number)*</label>
                <input type="text" name="prNumber" id="prNumber" required value="<?php echo htmlspecialchars($_POST['prNumber'] ?? ''); ?>">
                
                <label for="projectDetails">Project Details*</label>
                <textarea name="projectDetails" id="projectDetails" rows="4" required><?php echo htmlspecialchars($_POST['projectDetails'] ?? ''); ?></textarea>
                
                <label for="programOwner">Program Owner*</label>
                <input type="text" name="programOwner" id="programOwner" required placeholder="Enter Program Owner" value="<?php echo htmlspecialchars($_POST['programOwner'] ?? ''); ?>">

                <label for="programOffice">Program Owner Office*</label>
                <input type="text" name="programOffice" id="programOffice" required placeholder="Enter Program Owner Office" value="<?php echo htmlspecialchars($_POST['programOffice'] ?? ''); ?>">
                
                <label for="totalABC">Total ABC (Approved Budget for the Contract)*</label>
                <input type="number" name="totalABC" id="totalABC" required min="0" step="1" placeholder="Enter Total ABC" value="<?php echo htmlspecialchars($_POST['totalABC'] ?? ''); ?>">
                
                <button type="submit" name="addProject">Add Project</button>
            </form>
        </div>
    </div>

    <div id="statsModal" class="modal">
        <div class="modal-content stats-modal">
            <span class="close" id="statsClose">&times;</span>
            <div id="statsModalContentPlaceholder">
                <p style="text-align: center; margin-top: 20px;">Loading statistics...</p>
            </div>
        </div>
    </div>

    <script>
        // Define modal elements globally at the very top of your script
        const addProjectModal = document.getElementById('addProjectModal');
        const statsModal = document.getElementById('statsModal');
        const statsModalContentPlaceholder = document.getElementById('statsModalContentPlaceholder');
        const statsClose = document.getElementById('statsClose');
        const addProjectClose = document.getElementById('addProjectClose');
        const showAddProjectFormButton = document.getElementById('showAddProjectForm');
        
        // Pagination elements
        const prevPageBtn = document.getElementById('prevPage');
        const nextPageBtn = document.getElementById('nextPage');
        const linesPerPageSelect = document.getElementById('linesPerPage');
        
        // --- Common modal functions ---
        function closeModal(modal, contentPlaceholder = null) {
            if (modal) {
                modal.style.display = 'none';
                if (contentPlaceholder) {
                    contentPlaceholder.innerHTML = '';
                }
            }
        }
        
        // --- Show Add Project Modal on page load if there was an error ---
        // Pagination variables
        let currentPage = 1;
        let rowsPerPage = 10;
        let totalPages = 1;
        
        // Function to handle pagination
        function setupPagination() {
            const tableRows = document.querySelectorAll("table.dashboard-table tbody tr");
            
            // Count only rows that aren't filtered out by search
            const visibleRowsCount = Array.from(tableRows).filter(row => !row.classList.contains('filtered-out')).length;
            
            // Calculate total pages based on visible rows
            totalPages = Math.ceil(visibleRowsCount / rowsPerPage);
            
            // If current page is beyond total pages, reset to page 1
            if (currentPage > totalPages && totalPages > 0) {
                currentPage = 1;
            }
            
            // Update pagination buttons state
            updatePaginationControls();
            
            // Show only rows for current page
            displayRowsForCurrentPage();
        }
        
        // Function to display rows for current page
        function displayRowsForCurrentPage() {
            const tableRows = document.querySelectorAll("table.dashboard-table tbody tr");
            const displayStyle = window.matchMedia("(max-width: 500px)").matches ? "block" : "table-row";
            
            // Filter out rows that don't match search criteria
            const visibleRows = Array.from(tableRows).filter(row => !row.classList.contains('filtered-out'));
            
            // Calculate pagination based on visible rows
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            
            // Hide all rows first
            tableRows.forEach(row => {
                row.style.display = "none";
            });
            
            // Show only the rows for current page that aren't filtered out
            visibleRows.forEach((row, index) => {
                if (index >= startIndex && index < endIndex) {
                    row.style.display = displayStyle;
                }
            });
        }
        
        // Function to update pagination controls
        function updatePaginationControls() {
            // Disable prev button if on first page
            prevPageBtn.disabled = currentPage === 1;
            
            // Disable next button if on last page
            nextPageBtn.disabled = currentPage === totalPages || totalPages === 0;
        }
        
        // Event listener for previous page button
        prevPageBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                displayRowsForCurrentPage();
                updatePaginationControls();
            }
        });
        
        // Event listener for next page button
        nextPageBtn.addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                displayRowsForCurrentPage();
                updatePaginationControls();
            }
        });
        
        // Event listener for lines per page dropdown
        linesPerPageSelect.addEventListener('change', function() {
            rowsPerPage = parseInt(this.value);
            currentPage = 1; // Reset to first page when changing rows per page
            setupPagination();
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!empty($projectError)): ?>
                if (addProjectModal) {
                    addProjectModal.style.display = 'block';
                }
            <?php endif; ?>
            
            // Initialize pagination
            setupPagination();
        });

        // --- Modal Closing Logic (Escape Key) ---
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeModal(addProjectModal);
                closeModal(statsModal, statsModalContentPlaceholder);
            }
        });

        // --- Search functionality for filtering projects ---
        function performSearch() {
            let searchInput = document.getElementById("searchInput");
            let query = searchInput.value.toLowerCase().trim();
            let rows = document.querySelectorAll("table.dashboard-table tbody tr");
            let visibleCount = 0;
            
            // Mark rows as filtered or not based on search query
            rows.forEach(row => {
                // Use querySelector to reliably get the cells by their data-label attributes
                let prNumberCell = row.querySelector('[data-label="PR Number"]');
                let projectDetailsCell = row.querySelector('[data-label="Project Details"]');
                
                if (!prNumberCell || !projectDetailsCell) {
                    // Fallback to direct children if data-label selectors don't work
                    prNumberCell = row.children[0];
                    projectDetailsCell = row.children[1];
                }
                
                let prNumber = prNumberCell ? prNumberCell.textContent.toLowerCase() : '';
                let projectDetails = projectDetailsCell ? projectDetailsCell.textContent.toLowerCase() : '';
                
                if (prNumber.includes(query) || projectDetails.includes(query)) {
                    row.classList.remove('filtered-out');
                    visibleCount++;
                } else {
                    row.classList.add('filtered-out');
                }
            });
            
            const noResultsDiv = document.getElementById("noResults");
            // Only show "No results" if the search query is not empty and no rows are visible
            if (noResultsDiv) {
                noResultsDiv.style.display = (visibleCount === 0 && query !== '') ? "block" : "none";
            }
            
            // Reset to first page and update pagination
            currentPage = 1;
            setupPagination();
        }
        
        // Add event listeners for search input
        const searchInput = document.getElementById("searchInput");
        if (searchInput) {
            // Search on keyup
            searchInput.addEventListener("keyup", performSearch);
            
            // Also search when input is cleared or changed
            searchInput.addEventListener("input", performSearch);
            
            // Search on form submission
            searchInput.form?.addEventListener("submit", function(e) {
                e.preventDefault();
                performSearch();
            });
        }

        // --- Add Project Modal logic ---
        if (showAddProjectFormButton) {
            showAddProjectFormButton.addEventListener('click', function() {
                if (addProjectModal) {
                    addProjectModal.style.display = 'block';
                    // Clear any previous error messages when opening the modal for a new attempt
                    const errorParagraph = addProjectModal.querySelector('p[style*="color: red"]');
                    if (errorParagraph) {
                        errorParagraph.remove();
                    }
                    // Reset form fields when opening the modal for a new project
                    document.getElementById('addProjectForm').reset();
                }
            });
        }
        
        if (addProjectClose) {
            addProjectClose.addEventListener('click', function() {
                closeModal(addProjectModal);
            });
        }

        // --- Statistics Modal loading function ---
        function loadAndShowStatistics() {
            // Display a loading message immediately
            if (statsModalContentPlaceholder) {
                statsModalContentPlaceholder.innerHTML = '<p style="text-align: center; margin-top: 20px;">Loading statistics...</p>';
            }
            if (statsModal) {
                statsModal.style.display = 'block';
            }

            fetch('<?php echo url('statistics.php'); ?>')
                .then(response => {
                    if (!response.ok) {
                        console.error('Network response was not ok:', response.status, response.statusText);
                        return response.text().then(text => {
                            throw new Error('HTTP error! Status: ' + response.status + ' - ' + text);
                        });
                    }
                    return response.text();
                })
                .then(html => {
                    if (statsModalContentPlaceholder) {
                        statsModalContentPlaceholder.innerHTML = html;
                    }
                })
                .catch(error => {
                    console.error('There has been a problem with your fetch operation:', error);
                    if (statsModalContentPlaceholder) {
                        statsModalContentPlaceholder.innerHTML = '<p style="color: red; text-align: center; margin-top: 20px;">Failed to load statistics. Please try again.<br>Error: ' + error.message + '</p>';
                    }
                });
        }

        // --- Close Statistics Modal (X button) ---
        if (statsClose) {
            statsClose.addEventListener('click', function() {
                closeModal(statsModal, statsModalContentPlaceholder);
            });
        }

        // --- Handle clicks outside modals to close them ---
        document.addEventListener('click', function(event) {
            if (addProjectModal && event.target === addProjectModal) {
                closeModal(addProjectModal);
            }
            if (statsModal && event.target === statsModal) {
                closeModal(statsModal, statsModalContentPlaceholder);
            }
        });

        // --- Project details expand/collapse functions ---
        function showFullDetails(id) {
            document.getElementById(id + '_short').style.display = 'none';
            document.getElementById(id + '_full').style.display = 'inline';
        }
        
        function hideFullDetails(id) {
            document.getElementById(id + '_full').style.display = 'none';
            document.getElementById(id + '_short').style.display = 'inline';
        }
    </script>
</body>
</html>