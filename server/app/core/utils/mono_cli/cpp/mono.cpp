#include <iostream>
#include <cstdlib>
#include <string>

void handleOperation(const std::string& mode, const std::string& arg1, const std::string& arg2) {

    if (mode == "gen" || mode == "generate" || mode == "-g") {  
        std::string command = "php app/core/utils/mono_cli/cli.php \"gen\" " + arg1 + " " + arg2;
        system(command.c_str());
    } else if (mode == "serve" || mode == "-s") {
         std::string command = "php -S localhost:3000 public/index.php";
        system(command.c_str());
    } else if (mode == "fill" || mode == "-f") {
        std::string command = "php app/core/utils/mono_cli/cli.php \"fill\" " + arg1;
        system(command.c_str());
    } else {
        std::cout << "invalid mono command" << std::endl;
    }
}

int main(int argc, char* argv[]) {
    if (argc < 2) {
        std::cout << "Usage: program <mode> [arg1] [arg2]" << std::endl;
        return 1;
    }

    std::string mode = argv[1];
    std::string arg1 = (argc > 2) ? argv[2] : "";
    std::string arg2 = (argc > 3) ? argv[3] : "";

    handleOperation(mode, arg1, arg2);

    return 0;
}
