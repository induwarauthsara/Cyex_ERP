
const fs = require('fs');
const path = require('path');

function getDescription(filePath) {
    if (filePath.includes('vendor')) return 'External Dependency';
    if (filePath.endsWith('.css')) return 'Stylesheet';
    if (filePath.endsWith('.js')) return 'JavaScript Logic';
    if (filePath.endsWith('.png') || filePath.endsWith('.jpg') || filePath.endsWith('.svg')) return 'Image Asset';
    if (filePath.endsWith('.sql')) return 'Database Backup/Schema';
    if (filePath.endsWith('.md')) return 'Documentation';

    if (filePath.includes('api/v1')) {
        if (filePath.includes('login.php')) return 'API Authentication Endpoint';
        if (filePath.includes('list.php')) return 'API List Endpoint';
        if (filePath.includes('add.php')) return 'API Creation Endpoint';
        if (filePath.includes('update.php')) return 'API Update Endpoint';
        if (filePath.includes('delete.php')) return 'API Deletion Endpoint';
        return 'API Endpoint';
    }

    if (filePath.includes('AdminPanel')) {
        if (filePath.includes('hrm')) return 'HRM Module File';
        if (filePath.includes('expenses')) return 'Expenses Module File';
        if (filePath.includes('purchase')) return 'Purchase/Inventory Module';
        return 'Admin Panel Component';
    }

    if (filePath.includes('inc/')) return 'Include/Utility File';
    if (filePath.includes('config.php')) return 'Configuration File';
    if (filePath.includes('index.php')) return 'Main Entry Point / Dashboard';

    return 'System File';
}

try {
    const data = fs.readFileSync('DOC/all_files.txt', 'utf8');
    const lines = data.split(/\r?\n/);
    
    let content = "# System File Inventory\n\n";
    content += "| File Path | Inferred Description |\n";
    content += "| :--- | :--- |\n";

    lines.forEach(line => {
        let cleanPath = line.trim();
        if (!cleanPath) return;
        if (cleanPath.startsWith('./')) {
            cleanPath = cleanPath.substring(2);
        }
        
        const desc = getDescription(cleanPath);
        content += `| \`${cleanPath}\` | ${desc} |\n`;
    });

    fs.writeFileSync('DOC/File_Inventory.md', content);
    console.log("Inventory generated successfully.");

} catch (err) {
    console.error(err);
}
