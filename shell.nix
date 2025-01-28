{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  packages = with pkgs; [
    php84Packages.composer
    php84
    nodejs_22
  ];
}