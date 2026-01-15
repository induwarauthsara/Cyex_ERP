
import os

def get_description(path):
    filename = os.path.basename(path)
    if "vendor" in path: return "External Dependency"
    if path.endswith(".css"): return "Stylesheet"
    if path.endswith(".js"): return "JavaScript Logic"
    if path.endswith(".png") or path.endswith(".jpg") or path.endswith(".svg"): return "Image Asset"
    if path.endswith(".sql"): return "Database Backup/Schema"
    if path.endswith(".md"): return "Documentation"
    
    if "api/v1" in path:
        if "login.php" in path: return "API Authentication Endpoint"
        if "list.php" in path: return "API List Endpoint"
        if "add.php" in path: return "API Creation Endpoint"
        if "update.php" in path: return "API Update Endpoint"
        if "delete.php" in path: return "API Deletion Endpoint"
        return "API Endpoint"
        
    if "AdminPanel" in path:
        if "hrm" in path: return "HRM Module File"
        if "expenses" in path: return "Expenses Module File"
        if "purchase" in path: return "Purchase/Inventory Module"
        return "Admin Panel Component"
        
    if "inc/" in path: return "Include/Utility File"
    if "config.php" in path: return "Configuration File"
    if "index.php" in path: return "Main Entry Point / Dashboard"
    
    return "System File"

def main():
    try:
        with open('DOC/all_files.txt', 'r') as f:
            lines = f.readlines()
        
        with open('DOC/File_Inventory.md', 'w') as out:
            out.write("# System File Inventory\n\n")
            out.write("| File Path | Inferred Description |\n")
            out.write("| :--- | :--- |\n")
            
            for line in lines:
                path = line.strip()
                if not path: continue
                
                # Check if it looks like a file path
                if path.startswith("./"):
                    clean_path = path[2:] # Remove ./
                    desc = get_description(clean_path)
                    out.write(f"| `{clean_path}` | {desc} |\n")
                    
        print("Inventory generated successfully.")
            
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    main()
