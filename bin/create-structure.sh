#!/bin/bash
# Run this script from project root to create the module directory structure

modules=("User" "Match" "Betting")
layers=("Domain/Entity" "Domain/ValueObject" "Domain/Event" "Domain/Repository" "Domain/Exception" "Application/Command" "Application/Query" "Application/Handler" "Application/Facade" "Application/DTO" "Infrastructure/Persistence" "Infrastructure/EventListener" "UI/Controller" "UI/Form")

for module in "${modules[@]}"; do
    for layer in "${layers[@]}"; do
        mkdir -p "src/${module}/${layer}"
        touch "src/${module}/${layer}/.gitkeep"
    done
done

# Test directories
for module in "${modules[@]}"; do
    for test_layer in "Domain" "Application" "Infrastructure" "UI"; do
        mkdir -p "tests/${module}/${test_layer}"
        touch "tests/${module}/${test_layer}/.gitkeep"
    done
done

echo "✅ Module structure created for: ${modules[*]}"
